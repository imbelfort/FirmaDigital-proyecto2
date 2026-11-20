<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docente';
    protected $primaryKey = 'id_docente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_docente',
        'facultad',
        'departamento',
        'categoria_docente',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_docente', 'id_usuario');
    }
}
