<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndicadorGestion extends Model
{
    protected $table = 'indicador_gestion';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'meta_esperada',
        'valor_actual',
        'unidad_medida',
        'periodo',
        'estado',
        'fecha_actualizacion',
    ];
}
