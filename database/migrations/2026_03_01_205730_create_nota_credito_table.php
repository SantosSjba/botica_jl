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
        if (Schema::hasTable('nota_credito')) {
            return;
        }
        Schema::create('nota_credito', function (Blueprint $table) {
            $table->bigIncrements('idnota');
            $table->unsignedBigInteger('idconf')->nullable();
            $table->string('tipocomp', 10)->default('07');
            $table->unsignedBigInteger('idcliente')->nullable();
            $table->unsignedBigInteger('idusuario')->nullable();
            $table->unsignedBigInteger('idserie')->nullable();
            $table->date('fecha_emision');
            $table->decimal('op_gravadas', 12, 2)->default(0);
            $table->decimal('op_exoneradas', 12, 2)->default(0);
            $table->decimal('op_inafectas', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('serie_ref', 20)->nullable();
            $table->string('correlativo_ref', 20)->nullable();
            $table->string('codmotivo', 10)->default('01');
            $table->string('feestado', 20)->nullable();
            $table->unsignedBigInteger('idventa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nota_credito');
    }
};
