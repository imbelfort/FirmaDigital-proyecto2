<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('observacion_tramite')
            ->where('estado', 'ABIERTA')
            ->where('tipo_observacion', 'DOCUMENTO_ILEGIBLE')
            ->chunkById(100, function ($observaciones) {
                foreach ($observaciones as $observacion) {
                    $documentos = DB::table('documento_expediente')
                        ->where('id_tramite', $observacion->id_tramite)
                        ->get(['nombre_documento', 'estado_validacion', 'fecha_validacion'])
                        ->filter(fn ($doc) => str_starts_with($observacion->descripcion, "Recaudo '{$doc->nombre_documento}' observado: "));
                    if ($documentos->count() !== 1) {
                        continue;
                    }
                    $documento = $documentos->first();
                    if ($documento->estado_validacion !== 'APROBADO' || !$documento->fecha_validacion
                        || $documento->fecha_validacion <= $observacion->fecha_observacion) {
                        continue;
                    }

                    DB::table('observacion_tramite')->where('id', $observacion->id)
                        ->update(['estado' => 'SUBSANADA', 'fecha_subsanacion' => $documento->fecha_validacion]);

                    DB::table('tramite')->where('id', $observacion->id_tramite)->where('estado', 'OBSERVADO')
                        ->whereNotExists(fn ($query) => $query->from('observacion_tramite')
                            ->whereColumn('id_tramite', 'tramite.id')->where('estado', 'ABIERTA'))
                        ->whereNotExists(fn ($query) => $query->from('documento_expediente')
                            ->whereColumn('id_tramite', 'tramite.id')
                            ->where(fn ($estados) => $estados->where('estado_validacion', 'OBSERVADO')
                                ->orWhere(fn ($pendiente) => $pendiente->where('estado_validacion', 'PENDIENTE')
                                    ->whereNotNull('observacion_validacion'))))
                        ->update(['estado' => 'INICIADO']);
                }
            });
    }

    public function down(): void
    {
        // Las aprobaciones registradas siguen vigentes.
    }
};
