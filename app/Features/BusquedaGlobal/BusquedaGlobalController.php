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
        $usuarioId = $request->input('usuario_id') ?? session('usuario')->id_usuario ?? null;
        $usuarioActual = $usuarioId ? Usuario::find($usuarioId) : null;
        $esAlumno = $usuarioActual && $usuarioActual->rol === 'ALUMNO';

        if (strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'query' => $q,
                'results' => [
                    'acciones_sistema' => [],
                    'tipos_tramite' => [],
                    'tramites' => [],
                    'usuarios' => [],
                    'certificados' => [],
                ]
            ]);
        }

        // 1. Acciones de Configuración de Interfaz y Accesibilidad (Modo Oscuro, Alto Contraste, Temas)
        $accionesSistema = [
            // Acciones directas de interfaz / Accesibilidad
            ['titulo' => 'Activar / Alternar Modo Oscuro', 'categoria' => 'Ajuste Visual', 'tipo' => 'ACCION_DARK', 'icono' => 'Moon', 'keywords' => 'modo oscuro dark mode noche luz tema'],
            ['titulo' => 'Activar / Alternar Alto Contraste (Accesibilidad)', 'categoria' => 'Ajuste Visual', 'tipo' => 'ACCION_CONTRASTE', 'icono' => 'Eye', 'keywords' => 'alto contraste accesibilidad vista color'],
            ['titulo' => 'Cambiar Tema: UAGRM Azul y Rojo', 'categoria' => 'Tema Visual', 'tipo' => 'TEMA_UAGRM', 'icono' => 'Palette', 'keywords' => 'tema uagrm azul rojo color'],
            ['titulo' => 'Cambiar Tema: Esmeralda Académico', 'categoria' => 'Tema Visual', 'tipo' => 'TEMA_EMERALD', 'icono' => 'Palette', 'keywords' => 'tema esmeralda verde academico color'],
            ['titulo' => 'Cambiar Tema: Índigo Moderno', 'categoria' => 'Tema Visual', 'tipo' => 'TEMA_INDIGO', 'icono' => 'Palette', 'keywords' => 'tema indigo violeta purpura color'],
            ['titulo' => 'Ajustar Tamaño de Letra: Normal (16px)', 'categoria' => 'Accesibilidad', 'tipo' => 'FONT_NORMAL', 'icono' => 'Type', 'keywords' => 'tamaño fuente letra normal texto'],
            ['titulo' => 'Ajustar Tamaño de Letra: Grande (18.5px)', 'categoria' => 'Accesibilidad', 'tipo' => 'FONT_LG', 'icono' => 'Type', 'keywords' => 'tamaño fuente letra grande zoom texto'],
        ];

        // Solo personal/administrador ve accesos a monitoreo SLA
        if (!$esAlumno) {
            $accionesSistema = array_merge($accionesSistema, [
                ['titulo' => 'Monitoreo de Tiempos y Alertas SLA (72h)', 'categoria' => 'Monitoreo', 'tipo' => 'RUTA_REPORTES', 'icono' => 'Clock', 'keywords' => 'sla 72 horas demoras alertas tiempo reportes metricas'],
            ]);
        }

        $matchedMenu = array_filter($accionesSistema, function ($item) use ($q) {
            $busqueda = strtolower($q);
            return stripos($item['titulo'], $busqueda) !== false ||
                   stripos($item['categoria'], $busqueda) !== false ||
                   stripos($item['keywords'], $busqueda) !== false;
        });

        // 2. Tipos de Trámites del Catálogo Oficial
        // RESTRICCIÓN POR ROL: El catálogo para "Solicitar Trámite" SOLO corresponde a ALUMNO o DOCENTE.
        // Un Administrador General, Encargado o Vicerrector NO solicita trámites; ellos gestionan, auditan y firman expedientes.
        $puedeSolicitar = $usuarioActual && in_array($usuarioActual->rol, ['ALUMNO', 'DOCENTE']);
        $tiposTramite = [];
        if ($puedeSolicitar) {
            $tiposTramite = TipoTramite::where(function ($query) use ($q) {
                    $query->where('nombre', 'ilike', "%{$q}%")
                          ->orWhere('codigo', 'ilike', "%{$q}%")
                          ->orWhere('descripcion', 'ilike', "%{$q}%");
                })
                ->limit(5)
                ->get();
        }

        // 3. Trámites / Expedientes Registrados
        // PRIVACIDAD ESTRICTA: El alumno SOLO puede ver sus propios trámites (no los de otros estudiantes).
        $tramitesQuery = Tramite::with(['solicitante', 'tipoTramite', 'pago', 'certificado'])
            ->where(function ($query) use ($q) {
                $query->where('codigo_tramite', 'ilike', "%{$q}%")
                      ->orWhere('asunto', 'ilike', "%{$q}%")
                      ->orWhere('estado', 'ilike', "%{$q}%")
                      ->orWhereHas('tipoTramite', function ($sub) use ($q) {
                          $sub->where('nombre', 'ilike', "%{$q}%")
                              ->orWhere('codigo', 'ilike', "%{$q}%");
                      });
            });

        if ($esAlumno) {
            $tramitesQuery->where('id_usuario_solicitante', $usuarioActual->id_usuario);
        } else {
            // El personal administrativo sí puede buscar por nombre o registro del solicitante
            $tramitesQuery->orWhereHas('solicitante', function ($sub) use ($q) {
                $sub->where('nombre', 'ilike', "%{$q}%")
                    ->orWhere('apellidos', 'ilike', "%{$q}%")
                    ->orWhere('registro', 'ilike', "%{$q}%")
                    ->orWhere('ci', 'ilike', "%{$q}%");
            });
        }

        $tramites = $tramitesQuery->limit(10)->get();

        // 4. Usuarios (Alumnos, Docentes, Encargados)
        // PRIVACIDAD ESTRICTA: Un alumno NO tiene permiso de consultar personas ni registros ajenos.
        $usuarios = [];
        if (!$esAlumno) {
            $usuarios = Usuario::with(['tramites' => function ($t) {
                    $t->with('tipoTramite')->orderBy('id', 'desc')->limit(3);
                }])
                ->where(function ($query) use ($q) {
                    $query->where('nombre', 'ilike', "%{$q}%")
                          ->orWhere('apellidos', 'ilike', "%{$q}%")
                          ->orWhere('registro', 'ilike', "%{$q}%")
                          ->orWhere('ci', 'ilike', "%{$q}%")
                          ->orWhere('email', 'ilike', "%{$q}%")
                          ->orWhere('rol', 'ilike', "%{$q}%");
                })
                ->limit(8)
                ->get();
        }

        // 5. Certificados Oficiales con QR
        $certificadosQuery = CertificadoOficial::with('tramite.solicitante')
            ->where(function ($query) use ($q) {
                $query->where('codigo_verificacion', 'ilike', "%{$q}%")
                      ->orWhere('titulo_documento', 'ilike', "%{$q}%");
            });

        if ($esAlumno) {
            $certificadosQuery->whereHas('tramite', function ($t) use ($usuarioActual) {
                $t->where('id_usuario_solicitante', $usuarioActual->id_usuario);
            });
        }

        $certificados = $certificadosQuery->limit(6)->get();

        return response()->json([
            'success' => true,
            'query' => $q,
            'results' => [
                'acciones_sistema' => array_values($matchedMenu),
                'tipos_tramite' => $tiposTramite,
                'tramites' => $tramites,
                'usuarios' => $usuarios,
                'certificados' => $certificados,
            ]
        ]);
    }
}
