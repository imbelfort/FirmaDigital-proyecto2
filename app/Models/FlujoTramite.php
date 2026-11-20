<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlujoTramite extends Model
{
    protected $table = 'flujo_tramite';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'etapa_actual',
        'id_usuario_responsable',
        'fecha_inicio_etapa',
        'fecha_fin_etapa',
        'estado_etapa',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }

    public function responsable()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_responsable', 'id_usuario');
    }
}
