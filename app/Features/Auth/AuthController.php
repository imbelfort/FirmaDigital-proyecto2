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
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado o inactivo con el registro proporcionado.',
            ], 401);
        }

        // Verifica si la contraseña coincide (en texto plano como viene en BD o con Hash)
        $valid = ($request->password === $usuario->password) ||
                 ($request->password === $usuario->ci) ||
                 (Hash::check($request->password, $usuario->password));

        if (!$valid) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña incorrecta. Recuerde que su contraseña inicial es su C.I.',
            ], 401);
        }

        // Almacenar en sesión
        session(['usuario' => $usuario]);

        return response()->json([
            'success' => true,
            'message' => 'Bienvenido al Sistema de Firma Digital',
            'usuario' => $usuario,
        ]);
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
        session()->forget('usuario');
        return response()->json([
            'success' => true,
            'message' => 'Sesión finalizada exitosamente.',
        ]);
    }
}
