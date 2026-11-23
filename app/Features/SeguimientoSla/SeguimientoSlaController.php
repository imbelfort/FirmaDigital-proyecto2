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

        // Recalcular horas transcurridas y registrar alertas automáticas
        foreach ($monitoreos as $m) {
            if (!in_array($m->estado_sla, ['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'])) {
                $inicio = \Carbon\Carbon::parse($m->fecha_inicio_sla);
                $horas = round($inicio->diffInHours(now()), 1);
                $m->horas_transcurridas = $horas;

                $horasRestantes = $m->horas_sla_limite - $horas;

                if ($horas > $m->horas_sla_limite) {
                    $m->estado_sla = 'VENCIDO';

                    // Registrar alerta de vencimiento si no existe aún
                    $yaTieneVencido = AlertaSla::where('id_monitoreo', $m->id)
                        ->where('tipo_alerta', 'VENCIMIENTO_SLA')
                        ->exists();

                    if (!$yaTieneVencido) {
                        AlertaSla::create([
                            'id_monitoreo' => $m->id,
                            'id_tramite' => $m->id_tramite,
                            'tipo_alerta' => 'VENCIMIENTO_SLA',
                            'mensaje' => "El trámite {$m->tramite?->codigo_tramite} ha sobrepasado el plazo límite de {$m->horas_sla_limite}h. Horas transcurridas: {$horas}h.",
                            'fecha_alerta' => now(),
                            'notificado_a' => 'encargado@uagrm.edu.bo,vicerrector@uagrm.edu.bo',
                        ]);
                        $m->alertas_emitidas += 1;
                        $m->ultima_alerta = now();
                    }
                } elseif ($horasRestantes <= 24) {
                    $m->estado_sla = 'POR_VENCER';

                    // Registrar advertencia 24h si no existe
                    $yaTiene24h = AlertaSla::where('id_monitoreo', $m->id)
                        ->where('tipo_alerta', 'ADVERTENCIA_24H')
                        ->exists();

                    if (!$yaTiene24h) {
                        AlertaSla::create([
                            'id_monitoreo' => $m->id,
                            'id_tramite' => $m->id_tramite,
                            'tipo_alerta' => 'ADVERTENCIA_24H',
                            'mensaje' => "El trámite {$m->tramite?->codigo_tramite} está próximo a vencer. Quedan {$horasRestantes}h de las {$m->horas_sla_limite}h reglamentarias.",
                            'fecha_alerta' => now(),
                            'notificado_a' => 'encargado@uagrm.edu.bo',
                        ]);
                        $m->alertas_emitidas += 1;
                        $m->ultima_alerta = now();
                    }
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
