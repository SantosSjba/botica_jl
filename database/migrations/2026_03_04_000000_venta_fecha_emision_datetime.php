<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Cambia venta.fecha_emision de DATE a DATETIME para guardar hora de emisión.
 * Sin pérdida de datos: los valores existentes (solo fecha) pasan a datetime con 00:00:00.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }
        $table = 'venta';
        $column = 'fecha_emision';
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return;
        }
        DB::statement('ALTER TABLE venta MODIFY fecha_emision DATETIME NOT NULL');
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver !== 'mysql') {
            return;
        }
        if (!Schema::hasTable('venta') || !Schema::hasColumn('venta', 'fecha_emision')) {
            return;
        }
        // Revertir a DATE: se pierde la parte hora (se trunca a fecha).
        DB::statement('ALTER TABLE venta MODIFY fecha_emision DATE NOT NULL');
    }
};
