<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalleventa';

    public $timestamps = false;

    protected $fillable = [
        'idventa', 'item', 'idproducto', 'cantidad', 'valor_unitario', 'precio_unitario',
        'igv', 'porcentaje_igv', 'valor_total', 'importe_total', 'descuento',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'igv' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'importe_total' => 'decimal:2',
        'descuento' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idventa', 'idventa');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }
}
