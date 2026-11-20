<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DelegacionAutoridad extends Model
{
    protected $table = 'delegacion_autoridad';
    public $timestamps = false;

    protected $fillable = [
        'id_autoridad_titular',
        'id_autoridad_delegada',
        'resolucion',
        'motivo',
        'fecha_inicio',
        'fecha_fin',
        'alcance_firma',
        'estado',
        'fecha_creacion',
    ];

    public function titular()
    {
        return $this->belongsTo(Usuario::class, 'id_autoridad_titular', 'id_usuario');
    }

    public function delegado()
    {
        return $this->belongsTo(Usuario::class, 'id_autoridad_delegada', 'id_usuario');
    }
}
