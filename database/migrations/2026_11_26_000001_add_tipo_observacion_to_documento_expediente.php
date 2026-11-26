<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documento_expediente', function (Blueprint $table) {
            $table->string('tipo_observacion', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('documento_expediente', function (Blueprint $table) {
            $table->dropColumn('tipo_observacion');
        });
    }
};
