<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoTramite extends Model
{
    protected $table = 'pago_tramite';

    protected $fillable = [
        'id_tramite',
        'payment_number',
        'transaction_id',
        'monto',
        'moneda',
        'metodo_pago',
        'estado',
        'qr_base64',
        'checkout_url',
        'deep_link',
        'fecha_expiracion',
        'fecha_pago',
        'raw_callback_payload',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_expiracion' => 'datetime',
        'fecha_pago' => 'datetime',
    ];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class, 'id_tramite');
    }
}
