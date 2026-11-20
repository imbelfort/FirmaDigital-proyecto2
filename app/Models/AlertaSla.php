<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaSla extends Model
{
    protected $table = 'alerta_sla';
    public $timestamps = false;

    protected $fillable = [
        'id_monitoreo',
        'id_tramite',
        'tipo_alerta',
        'mensaje',
        'fecha_alerta',
        'notificado_a',
    ];

    public function monitoreo()
    {
        return $this->belongsTo(MonitoreoSla::class, 'id_monitoreo');
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }
}
