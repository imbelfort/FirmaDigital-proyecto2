<?php

namespace App\Features\BusquedaGlobal;

use App\Models\Tramite;
use App\Models\Usuario;
use App\Models\TipoTramite;
use App\Models\CertificadoOficial;
use Illuminate\Http\Request;

class BusquedaGlobalController
{
    public function search(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'query' => $q,
                'results' => [
                    'opciones_sistema' => [],
                    'tramites' => [],
                    'usuarios' => [],
                    'certificados' => [],
                ]
            ]);
        }

        // 1. Opciones del sistema (menús, acciones rápidas)
        $menuItems = [
            ['titulo' => 'Nuevo Trámite de Titulación', 'categoria' => 'Acción', 'ruta' => 'nuevo-tramite', 'icono' => 'FilePlus', 'roles' => ['ALUMNO', 'DOCENTE', 'ENCARGADO', 'VICERRECTOR']],
            ['titulo' => 'Bandeja de Firma Digital Consolidada', 'categoria' => 'Firma', 'ruta' => 'firma-digital', 'icono' => 'PenTool', 'roles' => ['VICERRECTOR', 'ENCARGADO']],
            ['titulo' => 'Firma Digital Remota con Token ADSIB', 'categoria' => 'Firma', 'ruta' => 'firma-digital', 'icono' => 'Key', 'roles' => ['VICERRECTOR', 'ENCARGADO']],
            ['titulo' => 'Monitoreo de Tiempos y Alertas SLA (72h)', 'categoria' => 'Monitoreo', 'ruta' => 'monitoreo-sla', 'icono' => 'Clock', 'roles' => ['VICERRECTOR', 'ENCARGADO', 'ADMINISTRATIVO']],
            ['titulo' => 'Portal de Seguimiento con PIN Estudiantil', 'categoria' => 'Portal', 'ruta' => 'portal-pin', 'icono' => 'Search', 'roles' => ['ALUMNO', 'DOCENTE', 'ENCARGADO', 'VICERRECTOR']],
            ['titulo' => 'Gestión de Delegación de Autoridad', 'categoria' => 'Administración', 'ruta' => 'delegaciones', 'icono' => 'ShieldCheck', 'roles' => ['VICERRECTOR', 'ENCARGADO']],
            ['titulo' => 'Gestión de Usuarios y Credenciales', 'categoria' => 'Administración', 'ruta' => 'usuarios', 'icono' => 'Users', 'roles' => ['ENCARGADO', 'ADMINISTRATIVO', 'VICERRECTOR']],
            ['titulo' => 'Reportes y Estadísticas Vicerrectorales', 'categoria' => 'Reportes', 'ruta' => 'reportes', 'icono' => 'BarChart3', 'roles' => ['VICERRECTOR', 'ENCARGADO', 'ADMINISTRATIVO']],
            ['titulo' => 'Guías Procesales y Requisitos de Grado', 'categoria' => 'Guías', 'ruta' => 'catalogo-tramites', 'icono' => 'BookOpen', 'roles' => ['ALUMNO', 'DOCENTE', 'ENCARGADO', 'VICERRECTOR']],
        ];

        $matchedMenu = array_filter($menuItems, function ($item) use ($q) {
            return stripos($item['titulo'], $q) !== false || stripos($item['categoria'], $q) !== false;
        });

        // 2. Trámites
        $tramites = Tramite::with(['solicitante', 'tipoTramite'])
            ->where(function ($query) use ($q) {
                $query->where('codigo_tramite', 'ilike', "%{$q}%")
                      ->orWhere('asunto', 'ilike', "%{$q}%")
                      ->orWhere('estado', 'ilike', "%{$q}%");
            })
            ->limit(8)
            ->get();

        // 3. Usuarios
        $usuarios = Usuario::where('nombre', 'ilike', "%{$q}%")
            ->orWhere('apellidos', 'ilike', "%{$q}%")
            ->orWhere('registro', 'ilike', "%{$q}%")
            ->orWhere('ci', 'ilike', "%{$q}%")
            ->orWhere('email', 'ilike', "%{$q}%")
            ->limit(8)
            ->get();

        // 4. Certificados
        $certificados = CertificadoOficial::with('tramite')
            ->where('codigo_verificacion', 'ilike', "%{$q}%")
            ->orWhere('titulo_documento', 'ilike', "%{$q}%")
            ->limit(8)
            ->get();

        return response()->json([
            'success' => true,
            'query' => $q,
            'results' => [
                'opciones_sistema' => array_values($matchedMenu),
                'tramites' => $tramites,
                'usuarios' => $usuarios,
                'certificados' => $certificados,
            ]
        ]);
    }
}
