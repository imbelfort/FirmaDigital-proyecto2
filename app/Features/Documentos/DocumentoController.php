<?php

namespace App\Features\Documentos;

use App\Models\DocumentoExpediente;
use App\Models\Tramite;
use App\Models\BitacoraTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentoController
{
    public function store(Request $request)
    {
        $request->validate([
            'id_tramite' => 'required|exists:tramite,id',
            'nombre_documento' => 'required|string|max:200',
        ]);

        $tramite = Tramite::findOrFail($request->id_tramite);

        $archivoUrl = $request->archivo_url;
        $hash = hash('sha256', $request->nombre_documento . time());

        if ($request->hasFile('archivo')) {
            $path = $request->file('archivo')->store('expedientes', 'public');
            $archivoUrl = asset('storage/' . $path);
            $hash = hash_file('sha256', $request->file('archivo')->getRealPath());
        } elseif (!$archivoUrl) {
            $archivoUrl = 'http://archivos.uagrm.edu.bo/exp/' . Str_slug($request->nombre_documento) . '.pdf';
        }

        $doc = DocumentoExpediente::create([
            'id_tramite' => $tramite->id,
            'id_requisito' => $request->id_requisito,
            'nombre_documento' => $request->nombre_documento,
            'tipo_documento' => 'PDF',
            'archivo_url' => $archivoUrl,
            'hash_documento' => $hash,
            'estado_validacion' => 'PENDIENTE',
        ]);

        BitacoraTramite::create([
            'id_tramite' => $tramite->id,
            'id_usuario' => session('usuario')->id_usuario ?? $tramite->id_usuario_solicitante,
            'accion' => 'SUBIO_DOCUMENTO',
            'detalle' => "Documento adjuntado: {$doc->nombre_documento} con Hash SHA-256",
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
            'observacion_validacion' => 'nullable|string',
        ]);

        $doc = DocumentoExpediente::findOrFail($id);
        $doc->estado_validacion = $request->estado_validacion;
        $doc->observacion_validacion = $request->observacion_validacion;
        $doc->fecha_validacion = now();
        $doc->save();

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
