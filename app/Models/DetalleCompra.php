<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detallecompra';

    public $timestamps = false;

    protected $fillable = ['idcompra', 'idproducto', 'cantidad', 'precio', 'importe'];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio' => 'decimal:2',
        'importe' => 'decimal:2',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'idcompra', 'idcompra');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }
}
