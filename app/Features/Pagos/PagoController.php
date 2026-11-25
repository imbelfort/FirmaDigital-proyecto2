<?php

namespace App\Features\Pagos;

use App\Models\Tramite;
use App\Models\PagoTramite;
use App\Models\BitacoraTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class PagoController
{
    protected PagoFacilService $pagoFacilService;

    public function __construct(PagoFacilService $pagoFacilService)
    {
        $this->pagoFacilService = $pagoFacilService;
    }

    private function autorizarSolicitante(Request $request, Tramite $tramite): void
    {
        $usuario = $request->session()->get('usuario');
        abort_unless($usuario, 401, 'Inicia sesión para gestionar el pago.');
        abort_unless((int) $usuario->id_usuario === (int) $tramite->id_usuario_solicitante, 403);
    }

    public function iniciarStripe(Request $request, $idTramite)
    {
        return DB::transaction(function () use ($request, $idTramite) {
            $tramite = Tramite::with('tipoTramite')->lockForUpdate()->findOrFail($idTramite);
            $this->autorizarSolicitante($request, $tramite);
            $pago = $tramite->pago()->firstOrNew();
            if ($pago->estado === 'COMPLETADO') {
                return response()->json(['pagado' => true]);
            }

            $stripe = $this->stripe();
            if ($pago->metodo_pago === 'STRIPE') {
                $sesion = $this->consultarStripe($tramite, $pago);
                if ($pago->estado === 'COMPLETADO') {
                    return response()->json(['pagado' => true]);
                }
                if ($sesion->status === 'open') {
                    return response()->json(['url' => $sesion->url]);
                }
                if (!$this->stripePermiteOtroCobro($pago, $sesion)) {
                    return response()->json(['message' => 'Stripe todavía está procesando el pago.'], 409);
                }
            } elseif ($pago->exists && $pago->metodo_pago === 'PAGOFACIL_QR') {
                if ($this->verificarQr($tramite, $pago)) {
                    return response()->json(['pagado' => true]);
                }
                abort_if(
                    !$pago->fecha_expiracion || $pago->fecha_expiracion->isFuture(),
                    409,
                    'Ya hay un QR vigente. Paga con ese QR o espera a que expire para usar Stripe.'
                );
            }

            $monto = (int) round((float) $tramite->tipoTramite?->costo_bs * 100);
            abort_if($monto <= 0, 422, 'El trámite no tiene un arancel válido.');
            $retorno = rtrim(config('app.url'), '/') . '/?pago_stripe=' . $tramite->id;
            $intento = hash('sha256', implode(':', [
                config('app.url'), $tramite->codigo_tramite, $pago->transaction_id, $pago->fecha_expiracion,
            ]));

            try {
                $sesion = $stripe->checkout->sessions->create([
                    'mode' => 'payment',
                    'payment_method_types' => ['card'],
                    'adaptive_pricing' => ['enabled' => false],
                    'client_reference_id' => (string) $tramite->id,
                    'payment_intent_data' => ['metadata' => ['tramite_id' => (string) $tramite->id]],
                    'line_items' => [[
                        'quantity' => 1,
                        'price_data' => [
                            'currency' => 'bob',
                            'unit_amount' => $monto,
                            'product_data' => ['name' => "Arancel universitario {$tramite->codigo_tramite}"],
                        ],
                    ]],
                    'success_url' => $retorno,
                    'cancel_url' => $retorno . '&stripe_cancelado=1',
                ], ['idempotency_key' => 'arancel-' . $intento]);
            } catch (ApiErrorException $e) {
                abort(502, 'No se pudo iniciar Stripe Checkout. Intenta nuevamente.');
            }

            $pago->fill([
                'payment_number' => 'PAGO-' . $tramite->codigo_tramite,
                'transaction_id' => $sesion->id,
                'monto' => $monto / 100,
                'moneda' => 'BOB',
                'metodo_pago' => 'STRIPE',
                'estado' => 'PENDIENTE',
                'qr_base64' => null,
                'checkout_url' => null,
                'deep_link' => null,
                'fecha_expiracion' => now()->setTimestamp($sesion->expires_at),
                'raw_callback_payload' => null,
            ]);
            $tramite->pago()->save($pago);

            return response()->json(['url' => $sesion->url]);
        });
    }

    public function stripeWebhook(Request $request)
    {
        $secreto = config('services.stripe.webhook_secret');
        abort_unless($secreto, 503);

        try {
            $evento = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                $secreto
            );
        } catch (SignatureVerificationException | \UnexpectedValueException $e) {
            abort(400, 'Notificación Stripe inválida.');
        }

        $objeto = $evento->data->object;
        if ($evento->type === 'payment_intent.payment_failed') {
            DB::transaction(function () use ($objeto) {
                $tramite = Tramite::lockForUpdate()->find($objeto->metadata->tramite_id ?? null);
                $pago = $tramite?->pago;
                if ($pago?->metodo_pago === 'STRIPE' && $pago->estado !== 'COMPLETADO') {
                    $this->consultarStripe($tramite, $pago);
                }
            });
        } elseif (in_array($evento->type, [
            'checkout.session.completed',
            'checkout.session.expired',
            'checkout.session.async_payment_succeeded',
            'checkout.session.async_payment_failed',
        ], true)) {
            DB::transaction(function () use ($evento, $objeto) {
                $tramite = Tramite::lockForUpdate()->find($objeto->client_reference_id);
                $pago = $tramite?->pago;
                if ($pago?->metodo_pago === 'STRIPE' && $pago->transaction_id === $objeto->id) {
                    $this->actualizarStripe($tramite, $pago, $objeto, $evento->type === 'checkout.session.async_payment_failed');
                }
            });
        }

        return response()->json(['recibido' => true]);
    }

    private function stripe(): StripeClient
    {
        abort_unless(
            config('services.stripe.secret') && config('services.stripe.webhook_secret'),
            503,
            'Stripe todavía no está configurado.'
        );

        return new StripeClient(config('services.stripe.secret'));
    }

    private function consultarStripe(Tramite $tramite, PagoTramite $pago): Session
    {
        try {
            $sesion = $this->stripe()->checkout->sessions->retrieve($pago->transaction_id, ['expand' => ['payment_intent']]);
        } catch (ApiErrorException $e) {
            abort(502, 'No se pudo verificar el estado en Stripe. Intenta nuevamente.');
        }

        $this->actualizarStripe($tramite, $pago, $sesion);

        return $sesion;
    }

    private function actualizarStripe(Tramite $tramite, PagoTramite $pago, Session $sesion, bool $falloDefinitivo = false): void
    {
        if ($sesion->payment_status === 'paid') {
            $this->acreditarStripe($tramite, $pago, $sesion);

            return;
        }

        if (in_array($pago->estado, ['COMPLETADO', 'CANCELADO', 'EXPIRADO', 'FALLIDO'], true)) {
            return;
        }

        $intento = $sesion->payment_intent ?? null;
        $estadoSesion = $sesion->status ?? null;
        $estado = match (true) {
            $estadoSesion === 'expired' => 'EXPIRADO',
            $falloDefinitivo => 'FALLIDO',
            is_object($intento) && $intento->status === 'canceled' => 'CANCELADO',
            is_object($intento) && $intento->status === 'requires_payment_method'
                && isset($intento->last_payment_error) => 'RECHAZADO',
            $estadoSesion === 'complete' || (is_object($intento) && $intento->status === 'processing') => 'PROCESANDO',
            default => $pago->estado,
        };

        if ($estado !== $pago->estado) {
            $pago->update(['estado' => $estado]);
        }
    }

    private function stripePermiteOtroCobro(PagoTramite $pago, Session $sesion): bool
    {
        return $sesion->status === 'expired'
            || ($sesion->status === 'complete' && in_array($pago->estado, ['FALLIDO', 'CANCELADO'], true));
    }

    public function cancelarStripe(Request $request, $idTramite)
    {
        return DB::transaction(function () use ($request, $idTramite) {
            $tramite = Tramite::lockForUpdate()->findOrFail($idTramite);
            $this->autorizarSolicitante($request, $tramite);
            $pago = $tramite->pago;
            abort_unless($pago?->metodo_pago === 'STRIPE', 404, 'No hay una sesión de Stripe para este trámite.');

            if ($pago->estado !== 'COMPLETADO') {
                $this->cerrarSesionStripe($tramite, $pago);
            }

            return $this->respuestaEstadoPago($pago);
        });
    }

    private function cerrarSesionStripe(Tramite $tramite, PagoTramite $pago): Session
    {
        $sesion = $this->consultarStripe($tramite, $pago);
        if ($sesion->status !== 'open' || in_array($pago->estado, ['COMPLETADO', 'PROCESANDO'], true)) {
            return $sesion;
        }

        try {
            $sesion = $this->stripe()->checkout->sessions->expire($sesion->id);
        } catch (ApiErrorException $e) {
            return $this->consultarStripe($tramite, $pago);
        }

        if ($sesion->status === 'expired') {
            $pago->update(['estado' => 'CANCELADO']);
        }

        return $sesion;
    }

    private function acreditarStripe(Tramite $tramite, PagoTramite $pago, Session $sesion): void
    {
        if ($pago->estado === 'COMPLETADO' || $sesion->payment_status !== 'paid') {
            return;
        }

        abort_unless(
            $pago->transaction_id === $sesion->id
            && (string) $tramite->id === $sesion->client_reference_id
            && $sesion->mode === 'payment'
            && strtolower($pago->moneda) === $sesion->currency
            && (int) round((float) $pago->monto * 100) === $sesion->amount_total,
            409,
            'Los datos del pago no coinciden con el arancel registrado.'
        );

        $this->acreditarPago($tramite, $pago, 'PAGO_VERIFICADO_STRIPE');
    }

    private function acreditarPago(Tramite $tramite, PagoTramite $pago, string $accion): void
    {
        $pago->update(['estado' => 'COMPLETADO', 'fecha_pago' => now()]);
        BitacoraTramite::create([
            'id_tramite' => $tramite->id,
            'id_usuario' => $tramite->id_usuario_solicitante,
            'accion' => $accion,
            'detalle' => "Pago {$pago->payment_number} confirmado por Bs. {$pago->monto}",
            'ip_origen' => request()->ip(),
            'fecha_evento' => now(),
        ]);
    }

    /**
     * Iniciar / Generar QR de pago para un trámite existente
     */
    public function generarQr(Request $request, $idTramite)
    {
        return DB::transaction(fn () => $this->generarQrBloqueado($request, $idTramite));
    }

    private function generarQrBloqueado(Request $request, $idTramite)
    {
        $tramite = Tramite::with(['solicitante', 'tipoTramite', 'pago'])->lockForUpdate()->findOrFail($idTramite);
        $this->autorizarSolicitante($request, $tramite);
        $pago = $tramite->pago;

        if ($pago?->metodo_pago === 'STRIPE' && $pago->estado !== 'COMPLETADO') {
            $sesion = $this->cerrarSesionStripe($tramite, $pago);
            if ($pago->estado !== 'COMPLETADO' && !$this->stripePermiteOtroCobro($pago, $sesion)) {
                return response()->json(['message' => 'No se pudo cerrar el cobro en Stripe. Verifica su estado antes de generar un QR.'], 409);
            }
        }

        if ($pago?->estado === 'COMPLETADO') {
            return response()->json(['pagado' => true]);
        }

        // Si ya existe un QR generado para este trámite:
        // 1. Si el QR sigue vigente (no ha vencido), lo REUTILIZAMOS para evitar llamadas innecesarias y no saturar la pasarela.
        // 2. Si ya expiró, entonces llamamos nuevamente a PagoFácil para generar un nuevo QR renovado.
        if ($pago?->metodo_pago === 'PAGOFACIL_QR') {
            $fechaExp = $pago->fecha_expiracion;

            if (!$fechaExp || $fechaExp->isFuture()) {
                return response()->json([
                    'success' => true,
                    'reutilizado' => true,
                    'pago' => $pago,
                    'qr_image_url' => null,
                ]);
            }
        }

        if ($pago?->metodo_pago === 'PAGOFACIL_QR' && $this->verificarQr($tramite, $pago)) {
            return response()->json(['pagado' => true]);
        }

        // El costo oficial proviene estrictamente de tipo_tramite.costo_bs de BD.md
        $monto = (float)($tramite->tipoTramite->costo_bs ?? 50.00);

        $solicitante = $tramite->solicitante;
        $paymentNumber = 'PAGO-' . $tramite->codigo_tramite;

        // Llamar servicio PagoFácil
        $qrData = $this->pagoFacilService->generateQr([
            'payment_number' => $paymentNumber,
            'client_name' => "{$solicitante->nombre} {$solicitante->apellidos}",
            'document_id' => $solicitante->ci,
            'phone_number' => $solicitante->telefono ?? '71000000',
            'email' => $solicitante->email,
            'client_code' => $solicitante->registro,
            'amount' => $monto,
            'concept' => "Arancel Universitario: {$tramite->asunto}",
        ]);

        if (empty($qrData['success'])) {
            return response()->json([
                'success' => false,
                'message' => $qrData['message'] ?? 'Error al comunicarse con la pasarela PagoFácil.',
            ], 422);
        }

        $pago ??= $tramite->pago()->firstOrNew();
        $pago->fill([
            'payment_number' => $paymentNumber,
            'transaction_id' => $qrData['transaction_id'] ?? null,
            'monto' => $monto,
            'moneda' => 'BOB',
            'metodo_pago' => 'PAGOFACIL_QR',
            'estado' => 'PENDIENTE',
            'qr_base64' => $qrData['qr_base64'] ?? null,
            'checkout_url' => $qrData['checkout_url'] ?? null,
            'deep_link' => $qrData['deep_link'] ?? null,
            'fecha_expiracion' => $qrData['expiration_date'] ?? now()->addDay(),
            'raw_callback_payload' => null,
        ]);
        $tramite->pago()->save($pago);

        BitacoraTramite::create([
            'id_tramite' => $tramite->id,
            'id_usuario' => session('usuario')->id_usuario ?? $solicitante->id_usuario,
            'accion' => 'GENERO_PAGO_QR',
            'detalle' => "Generación de código QR PagoFácil por Bs. {$monto} (Ref: {$paymentNumber})",
            'ip_origen' => request()->ip(),
            'fecha_evento' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Código QR PagoFácil generado con éxito.',
            'pago' => $pago,
            'qr_image_url' => $qrData['qr_image_url'] ?? null,
        ]);
    }

    public function verificarEstadoPago(Request $request, $idTramite)
    {
        return DB::transaction(function () use ($request, $idTramite) {
            $tramite = Tramite::lockForUpdate()->findOrFail($idTramite);
            $this->autorizarSolicitante($request, $tramite);
            $pago = $tramite->pago;
            abort_unless($pago, 404, 'No hay un pago registrado para este trámite.');

            if ($pago->estado !== 'COMPLETADO') {
                if ($pago->metodo_pago === 'STRIPE') {
                    $this->consultarStripe($tramite, $pago);
                } elseif ($pago->metodo_pago === 'PAGOFACIL_QR') {
                    $this->verificarQr($tramite, $pago);
                }
            }

            return $this->respuestaEstadoPago($pago);
        });
    }

    private function respuestaEstadoPago(PagoTramite $pago)
    {
        return response()->json([
            'success' => true,
            'pagado' => $pago->estado === 'COMPLETADO',
            'message' => match ($pago->estado) {
                'COMPLETADO' => 'Pago confirmado y acreditado.',
                'PROCESANDO' => 'Stripe sigue procesando el pago. Vuelve a verificar en unos minutos.',
                'RECHAZADO' => 'La tarjeta fue rechazada. Puedes volver a intentar el pago.',
                'FALLIDO' => 'Stripe confirmó que el pago falló. Puedes iniciar otro intento.',
                'EXPIRADO' => 'La sesión de pago venció. Puedes iniciar otro intento.',
                'CANCELADO' => 'El intento se canceló. Puedes iniciar otro pago.',
                default => 'El pago sigue pendiente.',
            },
            'pago' => $pago,
        ]);
    }

    private function verificarQr(Tramite $tramite, PagoTramite $pago): bool
    {
        $resultado = $this->pagoFacilService->queryTransaction($pago->payment_number);
        abort_if(!empty($resultado['error']), 502, 'No se pudo verificar el pago del QR.');

        $valores = $resultado['values'] ?? null;
        abort_unless(
            is_array($valores) && (isset($valores['paymentStatus']) || !empty($valores['paymentDate'])),
            502,
            'PagoFácil no devolvió un estado verificable.'
        );
        $estado = $valores['paymentStatus'] ?? null;
        if ($estado !== 1 && $estado !== 2 && empty($valores['paymentDate'])) {
            return false;
        }

        $this->acreditarPago($tramite, $pago, 'PAGO_VERIFICADO_PAGOFACIL');

        return true;
    }

    /**
     * Acreditar el arancel manualmente desde ventanilla.
     */
    public function confirmarPago(Request $request, $idTramite)
    {
        $usuario = $request->session()->get('usuario');
        abort_unless($usuario, 401, 'Inicia sesión para validar el pago.');
        abort_unless(in_array($usuario->rol, ['ENCARGADO', 'ADMINISTRATIVO'], true), 403, 'Tu perfil no puede validar pagos en ventanilla.');

        return DB::transaction(function () use ($idTramite, $request, $usuario) {
            $tramite = Tramite::with('tipoTramite')->lockForUpdate()->findOrFail($idTramite);
            $pago = $tramite->pago()->firstOrNew([], [
                'payment_number' => 'PAGO-' . $tramite->codigo_tramite,
                'monto' => $tramite->tipoTramite->costo_bs ?? 50.00,
                'moneda' => 'BOB',
            ]);

            if ($pago->metodo_pago === 'STRIPE' && $pago->estado !== 'COMPLETADO') {
                $sesion = $this->cerrarSesionStripe($tramite, $pago);
                if ($pago->estado !== 'COMPLETADO' && !$this->stripePermiteOtroCobro($pago, $sesion)) {
                    return response()->json(['message' => 'El cobro de Stripe sigue activo o en procesamiento. Vuelve a verificar antes de validar en ventanilla.'], 409);
                }
            }

            if ($pago->estado !== 'COMPLETADO') {
                $pago->metodo_pago = 'MANUAL_VENTANILLA';
                $pago->estado = 'COMPLETADO';
                $pago->fecha_pago = now();
                $pago->transaction_id = null;
                $pago->qr_base64 = null;
                $pago->checkout_url = null;
                $pago->deep_link = null;
                $pago->fecha_expiracion = null;
                $pago->raw_callback_payload = null;
                $tramite->pago()->save($pago);

                BitacoraTramite::create([
                    'id_tramite' => $tramite->id,
                    'id_usuario' => $usuario->id_usuario,
                    'accion' => 'PAGO_CONFIRMADO',
                    'detalle' => "Arancel de Bs. {$pago->monto} acreditado manualmente en ventanilla.",
                    'ip_origen' => $request->ip(),
                    'fecha_evento' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $pago->metodo_pago === 'MANUAL_VENTANILLA'
                    ? 'Arancel validado en ventanilla.'
                    : 'El arancel ya está acreditado por la pasarela de pago.',
                'tramite' => $tramite->setRelation('pago', $pago),
            ]);
        });
    }

    /**
     * Webhook Callback oficial de PagoFácil según Guía de Integración
     */
    public function callback(Request $request)
    {
        $pedidoId = $request->input('PedidoID') ?? $request->input('paymentNumber');

        if (is_string($pedidoId) && $pedidoId !== '') {
            $idTramite = PagoTramite::where('payment_number', $pedidoId)
                ->where('metodo_pago', 'PAGOFACIL_QR')->value('id_tramite');

            if ($idTramite) {
                DB::transaction(function () use ($idTramite, $pedidoId) {
                    $tramite = Tramite::lockForUpdate()->find($idTramite);
                    $pago = $tramite?->pago;
                    if ($pago?->metodo_pago === 'PAGOFACIL_QR'
                        && $pago->payment_number === $pedidoId
                        && $pago->estado !== 'COMPLETADO') {
                        $this->verificarQr($tramite, $pago);
                    }
                });
            }
        }

        // Acuse esperado por PagoFácil (HTTP 200)
        return response()->json([
            'error' => 0,
            'status' => 1,
            'message' => 'Notificación recibida',
            'values' => true,
        ]);
    }
}
