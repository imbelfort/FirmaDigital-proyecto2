<?php

namespace App\Features\Auditoria;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usuario;

class AuditoriaController
{
    /**
     * Devuelve las bitácoras del sistema (logins, fallos, tráfico, errores)
     * Exclusivo para el Administrador General
     */
    public function index(Request $request)
    {
        $tipo = $request->input('tipo');
        $query = DB::table('bitacora_sistema')->orderBy('id', 'desc');

        if ($tipo && $tipo !== 'TODOS') {
            $query->where('tipo_evento', $tipo);
        }

        $registros = $query->limit(100)->get();

        // Resumen y métricas para el Admin
        $totalLoginsExitosos = DB::table('bitacora_sistema')->where('tipo_evento', 'LOGIN_EXITOSO')->count();
        $totalLoginsFallidos = DB::table('bitacora_sistema')->where('tipo_evento', 'LOGIN_FALLIDO')->count();
        $totalTrafico = DB::table('bitacora_sistema')->where('tipo_evento', 'TRAFICO_OPERATIVO')->count();
        $totalAccesosDenegados = DB::table('bitacora_sistema')->where('tipo_evento', 'ACCESO_NO_AUTORIZADO')->count();

        return response()->json([
            'success' => true,
            'kpis' => [
                'logins_exitosos' => $totalLoginsExitosos,
                'logins_fallidos' => $totalLoginsFallidos,
                'trafico_operativo' => $totalTrafico,
                'bloqueos_seguridad' => $totalAccesosDenegados,
            ],
            'data' => $registros,
        ]);
    }

    /**
     * Contador de Visitas por Página / Módulo
     */
    public function registrarVisita(Request $request)
    {
        $pagina = $request->input('pagina', 'INICIO');

        $visita = DB::table('visitas_sitio')->where('pagina', $pagina)->first();

        if ($visita) {
            DB::table('visitas_sitio')->where('pagina', $pagina)->increment('total_visitas');
            $total = $visita->total_visitas + 1;
        } else {
            DB::table('visitas_sitio')->insert([
                'pagina' => $pagina,
                'total_visitas' => 1,
                'fecha_actual' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $total = 1;
        }

        $totalGlobal = DB::table('visitas_sitio')->sum('total_visitas');

        return response()->json([
            'success' => true,
            'pagina' => $pagina,
            'visitas_pagina' => $total,
            'visitas_globales' => $totalGlobal,
        ]);
    }
}
