<?php

namespace Tests\Unit;

use App\Models\TipoTramite;
use PHPUnit\Framework\TestCase;

class TipoTramiteAdjuntosTest extends TestCase
{
    public function test_existing_types_keep_the_default_upload_stages(): void
    {
        $tipo = new TipoTramite;

        $this->assertTrue($tipo->permiteAdjuntos('INICIADO'));
        $this->assertTrue($tipo->permiteAdjuntos('OBSERVADO'));
        $this->assertFalse($tipo->permiteAdjuntos('REQUISITOS_VALIDADOS'));
    }

    public function test_the_configured_stages_replace_the_defaults_and_compilation_closes_uploads(): void
    {
        $tipo = new TipoTramite(['etapas_adjuntos' => ['REQUISITOS_VALIDADOS', 'EXPEDIENTE_COMPILADO']]);

        $this->assertTrue($tipo->permiteAdjuntos('REQUISITOS_VALIDADOS'));
        $this->assertFalse($tipo->permiteAdjuntos('INICIADO'));
        $this->assertFalse($tipo->permiteAdjuntos('EXPEDIENTE_COMPILADO'));

        $tipo->etapas_adjuntos = [];
        $this->assertFalse($tipo->permiteAdjuntos('OBSERVADO'));
    }
}
