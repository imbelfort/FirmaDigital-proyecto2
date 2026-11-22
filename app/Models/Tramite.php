<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    protected $table = 'tramite';
    public $timestamps = false;

    protected $fillable = [
        'codigo_tramite',
        'id_usuario_solicitante',
        'id_tipo_tramite',
        'asunto',
        'estado',
        'prioridad',
        'fecha_solicitud',
        'fecha_finalizacion',
    ];

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_solicitante', 'id_usuario');
    }

    public function tipoTramite()
    {
        return $this->belongsTo(TipoTramite::class, 'id_tipo_tramite');
    }

    public function pago()
    {
        return $this->hasOne(PagoTramite::class, 'id_tramite');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoExpediente::class, 'id_tramite');
    }

    public function flujos()
    {
        return $this->hasMany(FlujoTramite::class, 'id_tramite')->orderBy('id', 'asc');
    }

    public function observaciones()
    {
        return $this->hasMany(ObservacionTramite::class, 'id_tramite')->orderBy('id', 'desc');
    }

    public function bitacoras()
    {
        return $this->hasMany(BitacoraTramite::class, 'id_tramite')->orderBy('id', 'desc');
    }

    public function firmaDigital()
    {
        return $this->hasOne(FirmaDigital::class, 'id_tramite');
    }

    public function despacho()
    {
        return $this->hasOne(DespachoLegajo::class, 'id_tramite');
    }

    public function certificado()
    {
        return $this->hasOne(CertificadoOficial::class, 'id_tramite');
    }

    public function portalSeguimiento()
    {
        return $this->hasOne(PortalSeguimiento::class, 'id_tramite');
    }

    public function monitoreoSla()
    {
        return $this->hasOne(MonitoreoSla::class, 'id_tramite');
    }
}
