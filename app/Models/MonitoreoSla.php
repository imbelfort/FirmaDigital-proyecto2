<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoreoSla extends Model
{
    protected $table = 'monitoreo_sla';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'horas_sla_limite',
        'fecha_inicio_sla',
        'fecha_limite_sla',
        'horas_transcurridas',
        'estado_sla',
        'alertas_emitidas',
        'ultima_alerta',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }

    public function alertas()
    {
        return $this->hasMany(AlertaSla::class, 'id_monitoreo');
    }
}
