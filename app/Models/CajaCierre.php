<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaCierre extends Model
{
    protected $table = 'caja_cierre';

    protected $primaryKey = 'idcaja_c';

    public $timestamps = false;

    protected $fillable = [
        'fecha', 'caja', 'turno', 'hora', 'usuario',
        'pagos_efectivo', 'pagos_tarjeta', 'pagos_deposito',
        'total_venta', 'monto_a', 'caja_sistema', 'efectivo_caja', 'diferencia',
    ];

    protected $casts = [
        'fecha' => 'date',
        'pagos_efectivo' => 'decimal:2',
        'pagos_tarjeta' => 'decimal:2',
        'pagos_deposito' => 'decimal:2',
        'total_venta' => 'decimal:2',
        'monto_a' => 'decimal:2',
        'caja_sistema' => 'decimal:2',
        'efectivo_caja' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];
}
