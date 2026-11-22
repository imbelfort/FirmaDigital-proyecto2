<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'registro',
        'ci',
        'password',
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'rol',
        'unidad_academica',
        'cargo',
        'estado',
        'fecha_creacion',
        'fecha_modificacion',
    ];

    protected $hidden = [
        'password',
    ];

    public function alumno()
    {
        return $this->hasOne(Alumno::class, 'id_alumno', 'id_usuario');
    }

    public function docente()
    {
        return $this->hasOne(Docente::class, 'id_docente', 'id_usuario');
    }

    public function autoridad()
    {
        return $this->hasOne(Autoridad::class, 'id_autoridad', 'id_usuario');
    }

    public function tramites()
    {
        return $this->hasMany(Tramite::class, 'id_usuario_solicitante', 'id_usuario');
    }
}
