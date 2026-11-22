<?php

namespace App\Features\Usuarios;

use App\Models\Usuario;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Autoridad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController
{
    public function index(Request $request)
    {
        $query = Usuario::with(['alumno', 'docente', 'autoridad'])->orderBy('id_usuario', 'desc');

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'ilike', "%{$s}%")
                  ->orWhere('apellidos', 'ilike', "%{$s}%")
                  ->orWhere('registro', 'ilike', "%{$s}%")
                  ->orWhere('ci', 'ilike', "%{$s}%")
                  ->orWhere('email', 'ilike', "%{$s}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'registro' => 'required|numeric|unique:usuario,registro',
            'ci' => 'required|numeric|unique:usuario,ci',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|unique:usuario,email',
            'rol' => 'required|in:VICERRECTOR,ENCARGADO,DOCENTE,ALUMNO,ADMINISTRATIVO',
        ]);

        return DB::transaction(function () use ($request) {
            $usuario = Usuario::create([
                'registro' => $request->registro,
                'ci' => $request->ci,
                'password' => $request->ci, // Regla de negocio: Password por defecto es CI
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'rol' => $request->rol,
                'unidad_academica' => $request->unidad_academica,
                'cargo' => $request->cargo,
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ]);

            if ($request->rol === 'ALUMNO') {
                Alumno::create([
                    'id_alumno' => $usuario->id_usuario,
                    'carrera' => $request->carrera ?? 'Ingenieria Informatica',
                    'facultad' => $request->unidad_academica ?? 'FICCT',
                    'semestre' => $request->semestre ?? 1,
                    'estado_academico' => 'REGULAR',
                ]);
            } elseif ($request->rol === 'DOCENTE') {
                Docente::create([
                    'id_docente' => $usuario->id_usuario,
                    'facultad' => $request->unidad_academica ?? 'FICCT',
                    'departamento' => $request->departamento ?? 'Computacion',
                    'categoria_docente' => 'TIEMPO_COMPLETO',
                ]);
            } elseif (in_array($request->rol, ['VICERRECTOR', 'ENCARGADO'])) {
                Autoridad::create([
                    'id_autoridad' => $usuario->id_usuario,
                    'tipo_autoridad' => $request->rol === 'VICERRECTOR' ? 'VICERRECTOR' : 'ENCARGADO_DESPACHO',
                    'resolucion_nombramiento' => $request->resolucion ?? 'RES-VR-2026-NUEVO',
                    'fecha_posesion' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => $usuario->load(['alumno', 'docente', 'autoridad']),
            ]);
        });
    }

    public function update(Request $request, $id)
    {
        $usuarioActual = session('usuario');
        $usuarioIdAuth = $request->header('X-User-Id');
        
        // Regla de Negocio: El Administrador no puede auto-editarse desde este módulo para evitar bloqueos accidentales
        if (($usuarioActual && $usuarioActual->id_usuario == $id) || ($usuarioIdAuth && $usuarioIdAuth == $id)) {
            return response()->json([
                'success' => false,
                'message' => 'Restricción de seguridad: El Administrador General no puede editar su propia cuenta desde este panel.',
            ], 403);
        }

        $usuario = Usuario::findOrFail($id);

        $usuario->update([
            'nombre' => $request->nombre ?? $usuario->nombre,
            'apellidos' => $request->apellidos ?? $usuario->apellidos,
            'email' => $request->email ?? $usuario->email,
            'telefono' => $request->telefono ?? $usuario->telefono,
            'unidad_academica' => $request->unidad_academica ?? $usuario->unidad_academica,
            'cargo' => $request->cargo ?? $usuario->cargo,
            'estado' => $request->estado ?? $usuario->estado,
            'fecha_modificacion' => now(),
        ]);

        if ($usuario->rol === 'ALUMNO' && $usuario->alumno) {
            $usuario->alumno->update([
                'carrera' => $request->carrera ?? $usuario->alumno->carrera,
                'facultad' => $request->facultad ?? $usuario->alumno->facultad,
                'semestre' => $request->semestre ?? $usuario->alumno->semestre,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'data' => $usuario->load(['alumno', 'docente', 'autoridad']),
        ]);
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        // Soft disable as per business logic
        $usuario->estado = 'INACTIVO';
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario dado de baja (desactivado) conforme a reglas de negocio.',
        ]);
    }
}
