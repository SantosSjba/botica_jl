<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_venta', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('idventa');
            $table->string('tipo_pago', 50); // EFECTIVO, YAPE, PLIN, TARJETA, etc.
            $table->decimal('monto', 14, 2);
            $table->decimal('recibo', 14, 2)->nullable(); // Solo para EFECTIVO: efectivo recibido
            $table->string('numope', 100)->nullable();  // Nº operación / referencia (YAPE, transferencia, etc.)
            $table->timestamps();

            $table->index('idventa');
            $table->index(['idventa', 'tipo_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_venta');
    }
};
