<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class RbacMiddleware
{
    /**
     * Matriz de Control de Acceso (RBAC):
     * Define qué roles tienen acceso a qué recursos.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Obtener usuario de sesión o de header/input
        $usuario = session('usuario');
        $usuarioId = $request->header('X-User-Id') ?? $request->input('auth_usuario_id');
        
        if (!$usuario && $usuarioId) {
            $usuario = Usuario::find($usuarioId);
        }

        // Si no hay usuario y se exigen roles específicos
        if (!$usuario) {
            $this->registrarAuditoria($request, null, 'ACCESO_DENEGADO', 'Intento de acceso no autenticado', 401);
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: Debe iniciar sesión con sus credenciales institucionales para realizar esta acción.',
            ], 401);
        }

        // 2. Verificar rol si se pasaron roles al middleware
        if (!empty($roles) && !in_array($usuario->rol, $roles)) {
            $this->registrarAuditoria($request, $usuario, 'ACCESO_NO_AUTORIZADO', "Rol {$usuario->rol} no autorizado para esta función. Roles requeridos: " . implode(', ', $roles), 403);
            return response()->json([
                'success' => false,
                'message' => 'Permiso insuficiente: Su perfil actual (' . $usuario->rol . ') no dispone de atribuciones para este módulo o función.',
                'rol_actual' => $usuario->rol,
                'roles_permitidos' => $roles,
            ], 403);
        }

        $response = $next($request);

        // Registro de tráfico operativo en auditoría para operaciones modificatorias (POST, PUT, DELETE)
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'])) {
            $this->registrarAuditoria($request, $usuario, 'TRAFICO_OPERATIVO', "Ejecución de acción {$request->method()} en {$request->path()}", $response->getStatusCode());
        }

        return $response;
    }

    private function registrarAuditoria(Request $request, ?Usuario $usuario, string $tipo, string $detalle, int $status): void
    {
        try {
            DB::table('bitacora_sistema')->insert([
                'id_usuario' => $usuario?->id_usuario,
                'registro' => $usuario?->registro ?? $request->input('registro'),
                'tipo_evento' => $tipo,
                'descripcion' => $detalle,
                'ip_origen' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 250),
                'ruta_accedida' => $request->path(),
                'metodo_http' => $request->method(),
                'status_code' => $status,
                'fecha_evento' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silencioso para no cortar el flujo principal
        }
    }
}
