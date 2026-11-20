<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BitacoraTramite extends Model
{
    protected $table = 'bitacora_tramite';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'id_usuario',
        'accion',
        'detalle',
        'ip_origen',
        'fecha_evento',
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
