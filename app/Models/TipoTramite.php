<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTramite extends Model
{
    protected $table = 'tipo_tramite';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tiempo_sla_horas',
        'costo_bs',
        'estado',
    ];

    public function requisitos()
    {
        return $this->hasMany(Requisito::class, 'id_tipo_tramite');
    }

    public function guiasProcesales()
    {
        return $this->hasMany(GuiaProcesal::class, 'id_tipo_tramite')->orderBy('paso_numero');
    }
}
