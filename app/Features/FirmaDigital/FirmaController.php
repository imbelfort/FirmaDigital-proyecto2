<?php

namespace App\Features\FirmaDigital;

use App\Models\Tramite;
use App\Models\FirmaDigital;
use App\Models\DespachoLegajo;
use App\Models\CertificadoOficial;
use App\Models\BitacoraTramite;
use App\Models\MonitoreoSla;
use App\Models\DelegacionAutoridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FirmaController
{
    public function delegaciones()
    {
        $delegaciones = DelegacionAutoridad::with(['titular', 'delegado'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $delegaciones,
        ]);
    }

    public function firmar(Request $request, $idTramite)
    {
        $request->validate([
            'tipo_firma' => 'required|in:CONSOLIDADA,REMOTA',
            'numero_certificado_digital' => 'required|string',
        ]);

        $tramite = Tramite::with('documentos')->findOrFail($idTramite);
        $usuario = session('usuario');
        $firmanteId = $usuario->id_usuario ?? 1;

        return DB::transaction(function () use ($tramite, $request, $firmanteId) {
            // Generar hash consolidado del expediente
            $concatDocs = $tramite->codigo_tramite . '|' . $tramite->asunto . '|' . now()->toIso8601String();
            foreach ($tramite->documentos as $doc) {
                $concatDocs .= '|' . $doc->hash_documento;
            }
            $hashExpediente = hash('sha256', $concatDocs);

            // Simulación criptográfica RSA Base64 con Certificado Oficial ADSIB / UAGRM
            $firmaValor = base64_encode(hash_hmac('sha256', $hashExpediente, 'UAGRM_CERT_KEY_SECURE_' . $request->numero_certificado_digital, true));

            $firma = FirmaDigital::updateOrCreate(
                ['id_tramite' => $tramite->id],
                [
                    'id_firmante' => $firmanteId,
                    'tipo_firma' => $request->tipo_firma,
                    'algoritmo' => 'SHA256withRSA',
                    'hash_documento' => $hashExpediente,
                    'firma_digital_valor' => $firmaValor,
                    'numero_certificado_digital' => $request->numero_certificado_digital,
                    'fecha_firma' => now(),
                    'valido' => true,
                ]
            );

            // Actualizar estado de trámite a FIRMADO (Falla 2 corregida)
            $tramite->estado = 'FIRMADO';
            $tramite->save();

            // Emitir Certificado Oficial con QR y UUID
            $uuid = (string) Str::uuid();
            $portalUrl = url("/portal/verificar/{$uuid}");
            $qrData = "UAGRM-FIRMA-OFICIAL|TRAMITE:{$tramite->codigo_tramite}|UUID:{$uuid}|VERIFICACION:{$portalUrl}";

            $cert = CertificadoOficial::updateOrCreate(
                ['id_tramite' => $tramite->id],
                [
                    'id_firma_digital' => $firma->id,
                    'codigo_verificacion' => $uuid,
                    'qr_data' => $qrData,
                    'titulo_documento' => 'CERTIFICACION Y TITULACION OFICIAL: ' . $tramite->asunto,
                    'fecha_emision' => now(),
                    'fecha_expiracion' => now()->addYears(5),
                    'portal_url' => $portalUrl,
                    'estado' => 'EMITIDO',
                ]
            );

            // Registrar Bitácora
            BitacoraTramite::create([
                'id_tramite' => $tramite->id,
                'id_usuario' => $firmanteId,
                'accion' => 'FIRMO_DIGITALMENTE',
                'detalle' => "Firma digital {$request->tipo_firma} ejecutada exitosamente con certificado {$request->numero_certificado_digital}. Certificado UUID: {$uuid}",
                'ip_origen' => request()->ip(),
                'fecha_evento' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Trámite {$tramite->codigo_tramite} firmado digitalmente con éxito ({$request->tipo_firma}).",
                'firma' => $firma,
                'certificado' => $cert,
            ]);
        });
    }

    public function despachar(Request $request, $idTramite)
    {
        $request->validate([
            'tipo_entrega' => 'required|in:DIGITAL_CORREO,PORTAL_DESCARGA,VENTANILLA',
        ]);

        $tramite = Tramite::with('monitoreoSla')->findOrFail($idTramite);
        $usuario = session('usuario');
        $despachadorId = $usuario->id_usuario ?? 2;

        return DB::transaction(function () use ($tramite, $request, $despachadorId) {
            $numDespacho = 'DESP-' . date('Y') . '-' . str_pad($tramite->id, 4, '0', STR_PAD_LEFT);
            $legajoUrl = url("/storage/legajos/{$tramite->codigo_tramite}_final.pdf");

            $despacho = DespachoLegajo::updateOrCreate(
                ['id_tramite' => $tramite->id],
                [
                    'id_usuario_despachador' => $despachadorId,
                    'numero_despacho' => $numDespacho,
                    'legajo_previsualizado_url' => $legajoUrl,
                    'tipo_entrega' => $request->tipo_entrega,
                    'fecha_despacho' => now(),
                    'estado' => 'DESPACHADO',
                ]
            );

            $tramite->estado = 'DESPACHADO';
            $tramite->fecha_finalizacion = now();
            $tramite->save();

            // Cerrar Monitoreo SLA
            if ($tramite->monitoreoSla) {
                $inicio = \Carbon\Carbon::parse($tramite->monitoreoSla->fecha_inicio_sla);
                $horas = $inicio->diffInHours(now());
                $tramite->monitoreoSla->horas_transcurridas = $horas;
                $tramite->monitoreoSla->estado_sla = ($horas <= $tramite->monitoreoSla->horas_sla_limite)
                    ? 'CUMPLIDO_EN_TIEMPO'
                    : 'CUMPLIDO_FUERA_DE_TIEMPO';
                $tramite->monitoreoSla->save();
            }

            BitacoraTramite::create([
                'id_tramite' => $tramite->id,
                'id_usuario' => $despachadorId,
                'accion' => 'DESPACHO',
                'detalle' => "Legajo despachado oficialmente con número {$numDespacho} vía {$request->tipo_entrega}.",
                'ip_origen' => request()->ip(),
                'fecha_evento' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Expediente despachado oficialmente con número {$numDespacho}.",
                'despacho' => $despacho,
            ]);
        });
    }
}
