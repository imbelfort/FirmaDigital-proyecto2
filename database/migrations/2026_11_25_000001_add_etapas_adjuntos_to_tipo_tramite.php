<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipo_tramite', function (Blueprint $table) {
            $table->json('etapas_adjuntos')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tipo_tramite', function (Blueprint $table) {
            $table->dropColumn('etapas_adjuntos');
        });
    }
};
