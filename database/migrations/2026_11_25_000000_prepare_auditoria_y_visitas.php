<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bitacora_sistema')) {
            Schema::create('bitacora_sistema', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_usuario')->nullable();
                $table->string('registro', 50)->nullable();
                $table->string('tipo_evento', 50);
                $table->text('descripcion');
                $table->ipAddress('ip_origen')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('ruta_accedida')->nullable();
                $table->string('metodo_http', 10)->nullable();
                $table->unsignedSmallInteger('status_code')->nullable();
                $table->timestamp('fecha_evento')->useCurrent();
            });
        }

        if (!Schema::hasTable('visitas_sitio')) {
            Schema::create('visitas_sitio', function (Blueprint $table) {
                $table->id();
                $table->string('pagina', 100);
                $table->unsignedBigInteger('total_visitas')->default(0);
                $table->date('fecha_actual');
                $table->timestamps();
            });
        }

        $duplicados = DB::table('visitas_sitio')
            ->selectRaw('pagina, MIN(id) AS conservar, SUM(total_visitas) AS total, MAX(fecha_actual) AS ultima_fecha, MAX(updated_at) AS ultima_actualizacion')
            ->groupBy('pagina')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicados as $pagina) {
            DB::table('visitas_sitio')->where('id', $pagina->conservar)->update([
                'total_visitas' => $pagina->total,
                'fecha_actual' => $pagina->ultima_fecha,
                'updated_at' => $pagina->ultima_actualizacion,
            ]);
            DB::table('visitas_sitio')->where('pagina', $pagina->pagina)->where('id', '<>', $pagina->conservar)->delete();
        }

        Schema::table('visitas_sitio', fn (Blueprint $table) => $table->unique('pagina', 'visitas_sitio_pagina_unica'));
        Schema::table('bitacora_sistema', fn (Blueprint $table) => $table->index(['tipo_evento', 'id'], 'bitacora_sistema_tipo_id_idx'));
    }

    public function down(): void
    {
        Schema::table('bitacora_sistema', fn (Blueprint $table) => $table->dropIndex('bitacora_sistema_tipo_id_idx'));
        Schema::table('visitas_sitio', fn (Blueprint $table) => $table->dropUnique('visitas_sitio_pagina_unica'));
    }
};
