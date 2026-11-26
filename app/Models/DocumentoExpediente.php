<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoExpediente extends Model
{
    public const TIPOS_OBSERVACION = ['DOCUMENTO_ILEGIBLE', 'DOCUMENTO_INCOMPLETO', 'DOCUMENTO_INCORRECTO', 'ERROR_DATOS', 'OTRO'];

    protected $table = 'documento_expediente';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'id_requisito',
        'nombre_documento',
        'tipo_documento',
        'archivo_url',
        'hash_documento',
        'estado_validacion',
        'observacion_validacion',
        'fecha_validacion',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }

    public function requisito()
    {
        return $this->belongsTo(Requisito::class, 'id_requisito');
    }
}
