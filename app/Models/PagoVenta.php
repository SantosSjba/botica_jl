<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoVenta extends Model
{
    protected $table = 'pago_venta';

    public $timestamps = true;

    protected $fillable = ['idventa', 'tipo_pago', 'monto', 'recibo', 'numope'];

    protected $casts = [
        'monto' => 'decimal:2',
        'recibo' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idventa', 'idventa');
    }
}
