<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificadoOficial extends Model
{
    protected $table = 'certificado_oficial';
    public $timestamps = false;

    protected $fillable = [
        'id_tramite',
        'id_firma_digital',
        'codigo_verificacion',
        'qr_data',
        'titulo_documento',
        'fecha_emision',
        'fecha_expiracion',
        'portal_url',
        'estado',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }

    public function firma()
    {
        return $this->belongsTo(FirmaDigital::class, 'id_firma_digital');
    }
}
