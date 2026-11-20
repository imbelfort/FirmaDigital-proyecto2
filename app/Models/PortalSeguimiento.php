<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSeguimiento extends Model
{
    protected $table = 'portal_seguimiento';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'pin_seguimiento',
        'portal_activo',
        'total_visitas',
        'ultimo_acceso',
    ];

    protected $casts = [
        'portal_activo' => 'boolean',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }
}
