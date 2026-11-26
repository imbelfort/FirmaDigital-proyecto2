<?php

namespace App\Features\Documentos;

use App\Models\DocumentoExpediente;
use App\Models\Tramite;
use App\Models\BitacoraTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DocumentoController
{
    public function archivo(Request $request, DocumentoExpediente $documento)
    {
        $usuario = $request->session()->get('usuario');
        abort_unless(
            $usuario && (int) $request->session()->get('usuario_autenticado_id') === (int) $usuario->id_usuario,
            401
        );
        abort_unless(
            in_array($usuario->rol, ['ENCARGADO', 'ADMINISTRATIVO'], true)
                || ($usuario->rol === 'ALUMNO'
                    && (int) $usuario->id_usuario === (int) $documento->tramite?->id_usuario_solicitante),
            403
        );

        $ruta = parse_url($documento->archivo_url ?? '', PHP_URL_PATH);
        abort_unless(is_string($ruta) && preg_match('~(?:^|/)storage/(expedientes/[a-zA-Z0-9_-]+\.(?:pdf|png|jpe?g))$~iD', $ruta, $partes), 404);

        $disco = Storage::disk('public');
        $archivo = $partes[1];
        abort_unless($disco->exists($archivo), 404, 'El archivo ya no está disponible.');

        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        $nombre = Str::slug(pathinfo($documento->nombre_documento, PATHINFO_FILENAME)) ?: 'adjunto';
        $tipo = match ($extension) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            default => 'image/jpeg',
        };

        return response()->file($disco->path($archivo), [
            'Content-Type' => $tipo,
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ])->setContentDisposition($request->boolean('descargar') ? 'attachment' : 'inline', "$nombre.$extension");
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tramite' => 'required|exists:tramite,id',
            'nombre_documento' => 'required|string|max:200',
            'id_documento_observado' => 'nullable|integer',
        ]);

        return DB::transaction(fn () => $this->guardarAdjunto($request));
    }

    private function guardarAdjunto(Request $request)
    {
        $tramite = Tramite::with('tipoTramite')->lockForUpdate()->findOrFail($request->id_tramite);
        $usuario = $request->session()->get('usuario');
        abort_unless(
            $usuario && (in_array($usuario->rol, ['ENCARGADO', 'ADMINISTRATIVO'], true)
                || (int) $usuario->id_usuario === (int) $tramite->id_usuario_solicitante),
            403
        );

        $documentoObservado = null;
        if ($request->filled('id_documento_observado')) {
            $documentoObservado = $tramite->documentos()->findOrFail($request->id_documento_observado);
            abort_unless($documentoObservado->estado_validacion === 'OBSERVADO', 422, 'El documento ya no está observado. Actualiza el expediente.');
        }

        $puedeCorregir = $documentoObservado && in_array($tramite->estado, ['INICIADO', 'OBSERVADO', 'REQUISITOS_VALIDADOS', 'EXPEDIENTE_COMPILADO'], true);
        if (!$puedeCorregir && !$tramite->tipoTramite?->permiteAdjuntos($tramite->estado)) {
            return response()->json([
                'success' => false,
                'message' => 'El tipo de trámite no admite adjuntos en esta etapa.',
            ], 422);
        }

        $request->validate([
            'id_requisito' => ['nullable', 'integer', Rule::exists('requisito', 'id')
                ->where('id_tipo_tramite', $tramite->id_tipo_tramite)],
            'archivo' => [
                $documentoObservado || $usuario->rol === 'ALUMNO' || $request->filled('id_requisito') ? 'required' : 'nullable',
                'file',
                'mimes:pdf,png,jpg,jpeg',
            ],
        ]);

        $archivoUrl = $request->archivo_url;
        $hash = hash('sha256', $request->nombre_documento . time());

        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('expedientes', 'public');
            $archivoUrl = asset('storage/' . $path);
            $hash = hash_file('sha256', $request->file('archivo')->getRealPath());
        } elseif (!$archivoUrl) {
            $archivoUrl = 'http://archivos.uagrm.edu.bo/exp/' . Str::slug($request->nombre_documento) . '.pdf';
        }

        $doc = $documentoObservado ?? new DocumentoExpediente([
            'id_tramite' => $tramite->id,
            'id_requisito' => $request->id_requisito,
            'nombre_documento' => $request->nombre_documento,
            'tipo_documento' => 'PDF',
        ]);
        $doc->fill([
            'archivo_url' => $archivoUrl,
            'hash_documento' => $hash,
            'estado_validacion' => 'PENDIENTE',
            'observacion_validacion' => $documentoObservado
                ? ($documentoObservado->observacion_validacion ?: 'Documento observado.')
                : null,
        ])->save();

        BitacoraTramite::create([
            'id_tramite' => $tramite->id,
            'id_usuario' => session('usuario')->id_usuario ?? $tramite->id_usuario_solicitante,
            'accion' => 'SUBIO_DOCUMENTO',
            'detalle' => $documentoObservado
                ? "Corrección enviada para {$doc->nombre_documento} con Hash SHA-256 {$hash}"
                : "Documento adjuntado: {$doc->nombre_documento} con Hash SHA-256",
            'ip_origen' => request()->ip(),
            'fecha_evento' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Documento agregado al expediente digital con firma hash.',
            'data' => $doc,
        ]);
    }

    public function validar(Request $request, $id)
    {
        $request->validate([
            'estado_validacion' => 'required|in:APROBADO,OBSERVADO,RECHAZADO',
            'observacion_validacion' => 'required_if:estado_validacion,OBSERVADO|nullable|string',
            'tipo_observacion' => ['required_if:estado_validacion,OBSERVADO', 'nullable', Rule::in(DocumentoExpediente::TIPOS_OBSERVACION)],
            'hash_documento' => 'nullable|string',
        ]);

        return DB::transaction(fn () => $this->validarAdjunto($request, $id));
    }

    private function validarAdjunto(Request $request, $id)
    {
        $tramite = Tramite::lockForUpdate()->findOrFail(DocumentoExpediente::findOrFail($id)->id_tramite);
        $doc = $tramite->documentos()->findOrFail($id);
        abort_unless(in_array($tramite->estado, ['INICIADO', 'OBSERVADO'], true), 422, 'La revisión del expediente ya está cerrada.');
        abort_if($request->filled('hash_documento') && $request->hash_documento !== $doc->hash_documento, 409, 'El archivo cambió. Actualiza el expediente antes de revisarlo.');

        if ($request->estado_validacion === 'APROBADO' && $doc->observacion_validacion) {
            $tramite->observaciones()->where('estado', 'ABIERTA')
                ->where('tipo_observacion', 'DOCUMENTO_ILEGIBLE')
                ->where('descripcion', "Recaudo '{$doc->nombre_documento}' observado: {$doc->observacion_validacion}")
                ->update(['estado' => 'SUBSANADA', 'fecha_subsanacion' => now()]);
        }

        $doc->estado_validacion = $request->estado_validacion;
        $doc->observacion_validacion = $request->observacion_validacion;
        $doc->tipo_observacion = $doc->estado_validacion === 'OBSERVADO' ? $request->tipo_observacion : null;
        $doc->fecha_validacion = now();
        $doc->save();

        if ($doc->estado_validacion === 'OBSERVADO') {
            $tramite->estado = 'OBSERVADO';
            $tramite->save();
        } elseif ($doc->estado_validacion === 'APROBADO' && $tramite->estado === 'OBSERVADO'
            && !$tramite->observaciones()->where('estado', 'ABIERTA')->exists()
            && !$tramite->documentos()->where(fn ($query) => $query->where('estado_validacion', 'OBSERVADO')
                ->orWhere(fn ($pendiente) => $pendiente->where('estado_validacion', 'PENDIENTE')
                    ->whereNotNull('observacion_validacion')))->exists()) {
            $tramite->estado = 'INICIADO';
            $tramite->save();
        }

        BitacoraTramite::create([
            'id_tramite' => $doc->id_tramite,
            'id_usuario' => session('usuario')->id_usuario ?? 2,
            'accion' => 'VALIDO_REQUISITO',
            'detalle' => "Documento '{$doc->nombre_documento}' evaluado como {$doc->estado_validacion}: {$doc->observacion_validacion}",
            'ip_origen' => request()->ip(),
            'fecha_evento' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Documento marcado como {$doc->estado_validacion}",
            'data' => $doc,
        ]);
    }
}
