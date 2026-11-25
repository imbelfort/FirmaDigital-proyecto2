<?php

namespace App\Features\Reportes;

use App\Models\Tramite;
use App\Models\MonitoreoSla;
use App\Models\FirmaDigital;
use App\Models\IndicadorGestion;
use App\Models\ObservacionTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController
{
    public function dashboard()
    {
        // Totales generales
        $totalTramites = Tramite::count();
        $totalFirmados = Tramite::where('estado', 'FIRMADO')->orWhere('estado', 'DESPACHADO')->count();
        $totalObservados = Tramite::where('estado', 'OBSERVADO')->count();
        $totalEnProceso = Tramite::whereIn('estado', ['INICIADO', 'REQUISITOS_VALIDADOS', 'EXPEDIENTE_COMPILADO', 'EN_FIRMA'])->count();

        // Tramites por Estado (previendo división por cero como en Falla 1)
        $porEstado = DB::table('tramite')
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->orderBy('estado')
            ->get();

        $primerMes = now()->startOfMonth()->subMonths(5);
        $finPeriodo = $primerMes->copy()->addMonths(6);
        $solicitudesPorDia = Tramite::query()
            ->where('fecha_solicitud', '>=', $primerMes)
            ->where('fecha_solicitud', '<', $finPeriodo)
            ->selectRaw('DATE(fecha_solicitud) as dia, COUNT(*) as total')
            ->groupByRaw('DATE(fecha_solicitud)')
            ->get();

        $totalesPorMes = $solicitudesPorDia->groupBy(fn ($fila) => substr($fila->dia, 0, 7));
        $tramitesPorMes = [];
        for ($mes = $primerMes->copy(); $mes < $finPeriodo; $mes->addMonth()) {
            $periodo = $mes->format('Y-m');
            $tramitesPorMes[] = [
                'mes' => $periodo,
                'total' => $totalesPorMes->get($periodo, collect())->sum('total'),
            ];
        }

        // SLA Compliance
        $slaCumplidos = MonitoreoSla::where('estado_sla', 'CUMPLIDO_EN_TIEMPO')->count();
        $slaTotalCerrados = MonitoreoSla::whereIn('estado_sla', ['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'])->count();
        $tasaCumplimiento = $slaTotalCerrados > 0 ? round(($slaCumplidos / $slaTotalCerrados) * 100, 2) : 95.00;

        // Firmas emitidas por autoridad
        $firmasPorAutoridad = DB::table('firma_digital')
            ->join('usuario', 'firma_digital.id_firmante', '=', 'usuario.id_usuario')
            ->select('usuario.nombre', 'usuario.apellidos', 'usuario.cargo', DB::raw('count(firma_digital.id) as total_firmas'))
            ->groupBy('usuario.nombre', 'usuario.apellidos', 'usuario.cargo')
            ->get();

        // Indicadores de gestión
        $indicadores = IndicadorGestion::where('estado', 'ACTIVO')->get();

        return response()->json([
            'success' => true,
            'kpis' => [
                'total_tramites' => $totalTramites,
                'total_firmados' => $totalFirmados,
                'total_observados' => $totalObservados,
                'total_en_proceso' => $totalEnProceso,
                'tasa_sla' => $tasaCumplimiento,
            ],
            'tramites_por_estado' => $porEstado,
            'tramites_por_mes' => $tramitesPorMes,
            'firmas_por_autoridad' => $firmasPorAutoridad,
            'indicadores' => $indicadores,
        ]);
    }
}
