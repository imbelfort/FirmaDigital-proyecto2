<?php

namespace App\Features\Pagos;

use App\Models\Tramite;
use App\Models\PagoTramite;
use App\Models\BitacoraTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagoController
{
    protected PagoFacilService $pagoFacilService;

    public function __construct(PagoFacilService $pagoFacilService)
    {
        $this->pagoFacilService = $pagoFacilService;
    }

    /**
     * Iniciar / Generar QR de pago para un trámite existente
     */
    public function generarQr(Request $request, $idTramite)
    {
        $tramite = Tramite::with(['solicitante', 'tipoTramite', 'pago'])->findOrFail($idTramite);

        // Si ya está pagado en la tabla de pagos
        if ($tramite->pago && $tramite->pago->estado === 'COMPLETADO') {
            return response()->json([
                'success' => false,
                'message' => 'Este trámite ya cuenta con pago acreditado y confirmado.',
                'pago' => $tramite->pago,
            ], 400);
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

        $pago = PagoTramite::updateOrCreate(
            ['id_tramite' => $tramite->id],
            [
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
            ]
        );

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

    /**
     * Verificar estado del pago consultando el servicio PagoFácil
     */
    public function verificarEstadoPago(Request $request, $idTramite)
    {
        $tramite = Tramite::with('pago')->findOrFail($idTramite);
        $pago = $tramite->pago;

        if (!$pago) {
            return response()->json([
                'success' => false,
                'pagado' => false,
                'message' => 'No se encontró registro de pago para este trámite.',
            ], 404);
        }

        if ($pago->estado === 'COMPLETADO') {
            return response()->json([
                'success' => true,
                'pagado' => true,
                'message' => 'El pago ya se encuentra acreditado y confirmado.',
                'pago' => $pago,
            ]);
        }

        // Consultar el estado real en PagoFácil
        $queryResult = $this->pagoFacilService->queryTransaction($pago->payment_number);

        if (!empty($queryResult['error']) && $queryResult['error'] != 0) {
            return response()->json([
                'success' => false,
                'pagado' => false,
                'message' => $queryResult['message'] ?? 'Error devuelto por la pasarela PagoFácil.',
            ], 422);
        }

        $statusValues = $queryResult['values'] ?? [];
        $paymentStatus = $statusValues['paymentStatus'] ?? null;

        // Si PagoFácil indica que fue pagado (según catálogo oficial)
        $estaPagadoEnPasarela = ($paymentStatus === 1 || $paymentStatus === 2 || !empty($statusValues['paymentDate']));

        if ($estaPagadoEnPasarela) {
            $pago->estado = 'COMPLETADO';
            $pago->fecha_pago = now();
            $pago->save();

            BitacoraTramite::create([
                'id_tramite' => $tramite->id,
                'id_usuario' => session('usuario')->id_usuario ?? $tramite->id_usuario_solicitante,
                'accion' => 'PAGO_VERIFICADO_PAGOFACIL',
                'detalle' => "Verificación de pago exitosa para {$pago->payment_number} por Bs. {$pago->monto}",
                'ip_origen' => request()->ip(),
                'fecha_evento' => now(),
            ]);

            return response()->json([
                'success' => true,
                'pagado' => true,
                'message' => "¡Pago verificado y confirmado exitosamente en PagoFácil!",
                'pago' => $pago,
            ]);
        }

        return response()->json([
            'success' => true,
            'pagado' => false,
            'message' => 'El pago aún está pendiente en la pasarela PagoFácil. Escanee el QR desde su app bancaria.',
            'pago' => $pago,
        ]);
    }

    /**
     * Confirmar pago (Manual / Simulado en Demo o verificado por banca móvil)
     */
    public function confirmarPago(Request $request, $idTramite)
    {
        $tramite = Tramite::with('pago')->findOrFail($idTramite);
        $pago = $tramite->pago;

        if (!$pago) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró registro de pago para este trámite.',
            ], 404);
        }

        return DB::transaction(function () use ($tramite, $pago) {
            $pago->estado = 'COMPLETADO';
            $pago->fecha_pago = now();
            $pago->save();

            BitacoraTramite::create([
                'id_tramite' => $tramite->id,
                'id_usuario' => session('usuario')->id_usuario ?? $tramite->id_usuario_solicitante,
                'accion' => 'PAGO_CONFIRMADO',
                'detalle' => "Pago acreditado exitosamente por Bs. {$pago->monto} vía PagoFácil QR",
                'ip_origen' => request()->ip(),
                'fecha_evento' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "¡Pago acreditado con éxito! El trámite {$tramite->codigo_tramite} ahora cuenta con arancel cancelado.",
                'tramite' => $tramite->load(['pago', 'tipoTramite']),
            ]);
        });
    }

    /**
     * Webhook Callback oficial de PagoFácil según Guía de Integración
     */
    public function callback(Request $request)
    {
        Log::info('PagoFacil Callback entrante:', $request->all());

        // Campos según la guía: PedidoID, Fecha, Hora, MetodoPago, Estado
        $pedidoId = $request->input('PedidoID') ?? $request->input('paymentNumber');

        if ($pedidoId) {
            $pago = PagoTramite::where('payment_number', $pedidoId)->first();
            if ($pago && $pago->estado !== 'COMPLETADO') {
                $pago->estado = 'COMPLETADO';
                $pago->fecha_pago = now();
                $pago->raw_callback_payload = json_encode($request->all());
                $pago->save();

                BitacoraTramite::create([
                    'id_tramite' => $pago->id_tramite,
                    'id_usuario' => 2, // Sistema / Mesa de Entrada
                    'accion' => 'PAGO_WEBHOOK_PAGOFACIL',
                    'detalle' => "Pago acreditado por notificación Webhook de PagoFácil (PedidoID: {$pedidoId})",
                    'ip_origen' => request()->ip(),
                    'fecha_evento' => now(),
                ]);
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
