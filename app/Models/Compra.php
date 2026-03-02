<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compra';

    protected $primaryKey = 'idcompra';

    public $timestamps = false;

    protected $fillable = [
        'idcliente', 'fecha', 'subtotal', 'igv', 'total', 'docu', 'num_docu',
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Cliente::class, 'idcliente', 'idcliente');
    }

    public function detalle()
    {
        return $this->hasMany(DetalleCompra::class, 'idcompra', 'idcompra');
    }
}
