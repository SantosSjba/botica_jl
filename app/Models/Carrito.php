<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Carrito de venta (tabla carrito). Session_id = usuario (login) del cajero.
 */
class Carrito extends Model
{
    protected $table = 'carrito';

    public $timestamps = false;

    protected $fillable = [
        'idproducto', 'descripcion', 'presentacion', 'cantidad', 'valor_unitario', 'precio_unitario',
        'igv', 'porcentaje_igv', 'valor_total', 'importe_total', 'operacion', 'session_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'valor_unitario' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'igv' => 'decimal:2',
        'valor_total' => 'decimal:2',
        'importe_total' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }
}
