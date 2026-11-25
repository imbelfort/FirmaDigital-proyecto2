<?php

namespace App\Features\Auth;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function login(Request $request)
    {
        $request->validate([
            'registro' => 'required|string',
            'password' => 'required|string',
        ]);

        $usuario = Usuario::with(['alumno', 'docente', 'autoridad'])
            ->where('registro', trim($request->registro))
            ->where('estado', 'ACTIVO')
            ->first();

        if (!$usuario) {
            $this->logAuditoria($request, null, $request->registro, 'LOGIN_FALLIDO', 'Intento de ingreso con registro inexistente o inactivo.', 401);
            return response()->json([
                'success' => false,
                'message' => 'Credencial no encontrada: El número de registro o código institucional no existe en el sistema universitario.',
            ], 401);
        }

        // Verifica si la contraseña coincide (en texto plano, con su C.I., con su propio código o con Hash)
        $valid = ($request->password === $usuario->password) ||
                 ($request->password === $usuario->ci) ||
                 ($request->password === $usuario->registro) ||
                 (Hash::check($request->password, $usuario->password));

        if (!$valid) {
            $this->logAuditoria($request, $usuario->id_usuario, $usuario->registro, 'LOGIN_FALLIDO', 'Contraseña incorrecta ingresada.', 401);
            return response()->json([
                'success' => false,
                'message' => 'Contraseña incorrecta: Verifique su clave de acceso. Recuerde que su contraseña inicial es su C.I.',
            ], 401);
        }

        // Almacenar en sesión
        $request->session()->regenerate();
        session(['usuario' => $usuario, 'usuario_autenticado_id' => $usuario->id_usuario]);

        $this->logAuditoria($request, $usuario->id_usuario, $usuario->registro, 'LOGIN_EXITOSO', "Sesión iniciada exitosamente con rol {$usuario->rol}.", 200);

        return response()->json([
            'success' => true,
            'message' => 'Bienvenido al Sistema de Firma Digital',
            'usuario' => $usuario,
        ]);
    }

    private function logAuditoria(Request $request, ?int $idUsuario, ?string $registro, string $tipo, string $desc, int $status): void
    {
        try {
            \Illuminate\Support\Facades\DB::table('bitacora_sistema')->insert([
                'id_usuario' => $idUsuario,
                'registro' => $registro,
                'tipo_evento' => $tipo,
                'descripcion' => $desc,
                'ip_origen' => $request->ip(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 250),
                'ruta_accedida' => $request->path(),
                'metodo_http' => $request->method(),
                'status_code' => $status,
                'fecha_evento' => now(),
            ]);
        } catch (\Throwable $e) {}
    }

    public function user(Request $request)
    {
        $user = session('usuario');
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado',
            ], 401);
        }

        $freshUser = Usuario::with(['alumno', 'docente', 'autoridad'])->find($user->id_usuario);
        session(['usuario' => $freshUser]);

        return response()->json([
            'success' => true,
            'usuario' => $freshUser,
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        return response()->json([
            'success' => true,
            'message' => 'Sesión finalizada exitosamente.',
        ]);
    }
}
