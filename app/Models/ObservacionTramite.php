<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObservacionTramite extends Model
{
    protected $table = 'observacion_tramite';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'id_usuario',
        'tipo_observacion',
        'descripcion',
        'estado',
        'fecha_observacion',
        'fecha_subsanacion',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }
}
