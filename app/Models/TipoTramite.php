<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTramite extends Model
{
    public const ETAPAS_ADJUNTOS_PERMITIDAS = ['INICIADO', 'OBSERVADO', 'REQUISITOS_VALIDADOS'];

    public const ETAPAS_ADJUNTOS_PREDETERMINADAS = ['INICIADO', 'OBSERVADO'];

    protected $table = 'tipo_tramite';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tiempo_sla_horas',
        'costo_bs',
        'estado',
        'etapas_adjuntos',
    ];

    protected $casts = [
        'etapas_adjuntos' => 'array',
    ];

    public function permiteAdjuntos(string $estado): bool
    {
        return in_array($estado, self::ETAPAS_ADJUNTOS_PERMITIDAS, true)
            && in_array($estado, $this->etapas_adjuntos ?? self::ETAPAS_ADJUNTOS_PREDETERMINADAS, true);
    }

    public function requisitos()
    {
        return $this->hasMany(Requisito::class, 'id_tipo_tramite');
    }

    public function guiasProcesales()
    {
        return $this->hasMany(GuiaProcesal::class, 'id_tipo_tramite')->orderBy('paso_numero');
    }
}
