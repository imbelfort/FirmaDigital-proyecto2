<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisito extends Model
{
    protected $table = 'requisito';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_tramite',
        'nombre',
        'descripcion',
        'es_obligatorio',
        'formato_permitido',
    ];

    protected $casts = [
        'es_obligatorio' => 'boolean',
    ];

    public function tipoTramite()
    {
        return $this->belongsTo(TipoTramite::class, 'id_tipo_tramite');
    }
}
