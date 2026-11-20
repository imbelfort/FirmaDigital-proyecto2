<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuiaProcesal extends Model
{
    protected $table = 'guia_procesal';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_tramite',
        'paso_numero',
        'nombre_etapa',
        'descripcion_instrucciones',
        'rol_responsable',
        'tiempo_estimado_horas',
    ];

    public function tipoTramite()
    {
        return $this->belongsTo(TipoTramite::class, 'id_tipo_tramite');
    }
}
