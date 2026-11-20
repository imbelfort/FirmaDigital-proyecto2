<?php

namespace App\Features\SeguimientoSla;

use App\Models\MonitoreoSla;
use App\Models\AlertaSla;
use App\Models\PortalSeguimiento;
use App\Models\CertificadoOficial;
use App\Models\Tramite;
use Illuminate\Http\Request;

class SeguimientoSlaController
{
    public function monitoreo()
    {
        $monitoreos = MonitoreoSla::with(['tramite.solicitante', 'tramite.tipoTramite', 'alertas'])
            ->orderBy('id', 'desc')
            ->get();

        // Recalcular horas transcurridas en tiempo real
        foreach ($monitoreos as $m) {
            if (!in_array($m->estado_sla, ['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'])) {
                $inicio = \Carbon\Carbon::parse($m->fecha_inicio_sla);
                $horas = $inicio->diffInHours(now());
                $m->horas_transcurridas = $horas;
                if ($horas > $m->horas_sla_limite) {
                    $m->estado_sla = 'VENCIDO';
                } elseif (($m->horas_sla_limite - $horas) <= 24) {
                    $m->estado_sla = 'POR_VENCER';
                } else {
                    $m->estado_sla = 'EN_TIEMPO';
                }
                $m->save();
            }
        }

        return response()->json([
            'success' => true,
            'data' => $monitoreos,
        ]);
    }

    public function consultarPin(Request $request, $pin)
    {
        $portal = PortalSeguimiento::with([
            'tramite.solicitante',
            'tramite.tipoTramite',
            'tramite.flujos',
            'tramite.observaciones',
            'tramite.firmaDigital.firmante',
            'tramite.certificado',
            'tramite.monitoreoSla'
        ])
        ->where('pin_seguimiento', trim($pin))
        ->first();

        if (!$portal) {
            return response()->json([
                'success' => false,
                'message' => 'El PIN ingresado no corresponde a ningún trámite activo.',
            ], 404);
        }

        $portal->total_visitas += 1;
        $portal->ultimo_acceso = now();
        $portal->save();

        return response()->json([
            'success' => true,
            'data' => $portal,
        ]);
    }

    public function verificarCertificado(Request $request, $uuid)
    {
        $cert = CertificadoOficial::with([
            'tramite.solicitante',
            'tramite.tipoTramite',
            'firma.firmante'
        ])
        ->where('codigo_verificacion', trim($uuid))
        ->first();

        if (!$cert) {
            return response()->json([
                'success' => false,
                'message' => 'Certificado no encontrado o inválido.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $cert,
        ]);
    }
}
