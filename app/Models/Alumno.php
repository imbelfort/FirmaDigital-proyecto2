<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumno';
    protected $primaryKey = 'id_alumno';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_alumno',
        'carrera',
        'facultad',
        'semestre',
        'estado_academico',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_alumno', 'id_usuario');
    }
}
