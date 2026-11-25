<?php

namespace App\Features\Auditoria;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriaController
{
    /**
     * Devuelve las bitácoras del sistema (logins, fallos, tráfico, errores)
     * Exclusivo para el Administrador General
     */
    public function index(Request $request)
    {
        $usuario = $request->session()->get('usuario');
        abort_unless($usuario && $request->session()->get('usuario_autenticado_id') === $usuario->id_usuario, 401, 'Inicia sesión para consultar la bitácora.');
        abort_unless($usuario->rol === 'ADMINISTRATIVO', 403, 'La bitácora está reservada a Administración.');

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

        $visitasPorPagina = DB::table('visitas_sitio')
            ->orderByDesc('total_visitas')
            ->orderBy('pagina')
            ->limit(10)
            ->get(['pagina', 'total_visitas']);

        $operacionesPorRuta = DB::table('bitacora_sistema')
            ->where('tipo_evento', 'TRAFICO_OPERATIVO')
            ->whereNotNull('ruta_accedida')
            ->select('ruta_accedida', 'metodo_http')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('ruta_accedida', 'metodo_http')
            ->orderByDesc('total')
            ->orderBy('ruta_accedida')
            ->orderBy('metodo_http')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'kpis' => [
                'logins_exitosos' => $totalLoginsExitosos,
                'logins_fallidos' => $totalLoginsFallidos,
                'trafico_operativo' => $totalTrafico,
                'bloqueos_seguridad' => $totalAccesosDenegados,
                'visitas_globales' => (int) DB::table('visitas_sitio')->sum('total_visitas'),
            ],
            'visitas_por_pagina' => $visitasPorPagina,
            'operaciones_por_ruta' => $operacionesPorRuta,
            'data' => $registros,
        ]);
    }

    /**
     * Contador de Visitas por Página / Módulo
     */
    public function registrarVisita(Request $request)
    {
        $validado = $request->validate(['pagina' => 'sometimes|required|string|max:100']);
        $pagina = $validado['pagina'] ?? 'INICIO';
        $usuario = $request->session()->get('usuario');
        if ($usuario && $request->session()->get('usuario_autenticado_id') !== $usuario->id_usuario) {
            $usuario = null;
        }

        $total = DB::transaction(function () use ($pagina, $request, $usuario) {
            $ahora = now();
            DB::table('visitas_sitio')->insertOrIgnore([
                'pagina' => $pagina,
                'total_visitas' => 0,
                'fecha_actual' => $ahora->toDateString(),
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
            $contador = DB::table('visitas_sitio')->where('pagina', $pagina);
            $contador->increment('total_visitas', 1, [
                'fecha_actual' => $ahora->toDateString(),
                'updated_at' => $ahora,
            ]);
            DB::table('bitacora_sistema')->insert([
                'id_usuario' => $usuario?->id_usuario,
                'registro' => $usuario?->registro,
                'tipo_evento' => 'VISITA_PAGINA',
                'descripcion' => "Visita a {$pagina}",
                'ruta_accedida' => $pagina,
                'metodo_http' => $request->method(),
                'ip_origen' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 250),
                'status_code' => 200,
                'fecha_evento' => $ahora,
            ]);

            return (int) $contador->value('total_visitas');
        });

        $totalGlobal = (int) DB::table('visitas_sitio')->sum('total_visitas');

        return response()->json([
            'success' => true,
            'pagina' => $pagina,
            'visitas_pagina' => $total,
            'visitas_globales' => $totalGlobal,
        ]);
    }
}
