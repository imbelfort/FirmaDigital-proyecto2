<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirmaDigital extends Model
{
    protected $table = 'firma_digital';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'id_firmante',
        'tipo_firma',
        'algoritmo',
        'hash_documento',
        'firma_digital_valor',
        'numero_certificado_digital',
        'fecha_firma',
        'valido',
    ];

    protected $casts = [
        'valido' => 'boolean',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }

    public function firmante()
    {
        return $this->belongsTo(Usuario::class, 'id_firmante');
    }
}
