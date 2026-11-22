<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Agregar columnas de pago a la tabla tramite si no existen
        Schema::table('tramite', function (Blueprint $table) {
            if (!Schema::hasColumn('tramite', 'costo_total')) {
                $table->decimal('costo_total', 10, 2)->default(0.00);
            }
            if (!Schema::hasColumn('tramite', 'estado_pago')) {
                $table->string('estado_pago', 30)->default('PENDIENTE'); // PENDIENTE, PAGADO, EXENTO
            }
        });

        // 2. Crear tabla de pagos (PagoFacil QR)
        Schema::create('pago_tramite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tramite');
            $table->string('payment_number', 50)->unique(); // Ref del comercio (ej: PAGO-TR-2026-0001)
            $table->string('transaction_id', 100)->nullable(); // ID retornado por PagoFacil
            $table->decimal('monto', 10, 2);
            $table->string('moneda', 10)->default('BOB');
            $table->string('metodo_pago', 50)->default('PAGOFACIL_QR');
            $table->string('estado', 30)->default('PENDIENTE'); // PENDIENTE, COMPLETADO, EXPIRADO, RECHAZADO
            $table->text('qr_base64')->nullable(); // Imagen Base64 del QR devuelto
            $table->text('checkout_url')->nullable();
            $table->text('deep_link')->nullable();
            $table->timestamp('fecha_expiracion')->nullable();
            $table->timestamp('fecha_pago')->nullable();
            $table->text('raw_callback_payload')->nullable();
            $table->timestamps();

            $table->foreign('id_tramite')->references('id')->on('tramite')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_tramite');
        Schema::table('tramite', function (Blueprint $table) {
            $table->dropColumn(['costo_total', 'estado_pago']);
        });
    }
};
