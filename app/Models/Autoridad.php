<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autoridad extends Model
{
    protected $table = 'autoridad';
    protected $primaryKey = 'id_autoridad';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_autoridad',
        'tipo_autoridad',
        'resolucion_nombramiento',
        'fecha_posesion',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_autoridad', 'id_usuario');
    }
}
