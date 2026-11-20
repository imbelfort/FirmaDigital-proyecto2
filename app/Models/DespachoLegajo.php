<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DespachoLegajo extends Model
{
    protected $table = 'despacho_legajo';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'id_usuario_despachador',
        'numero_despacho',
        'legajo_previsualizado_url',
        'tipo_entrega',
        'fecha_despacho',
        'estado',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }

    public function despachador()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_despachador');
    }
}
