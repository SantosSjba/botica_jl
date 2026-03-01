<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $primaryKey = 'idproducto';

    public $timestamps = false;

    protected $fillable = [
        'codigo', 'idlote', 'descripcion', 'tipo', 'stock', 'stockminimo',
        'precio_compra', 'precio_venta', 'descuento', 'ventasujeta', 'fecha_registro',
        'reg_sanitario', 'idcategoria', 'idpresentacion', 'idcliente', 'idsintoma',
        'idunidad', 'idtipoaf', 'estado', 'tipo_precio',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
        'stock' => 'integer',
        'stockminimo' => 'integer',
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'idlote', 'idlote');
    }

    public function presentacion()
    {
        return $this->belongsTo(Presentacion::class, 'idpresentacion', 'idpresentacion');
    }

    public function sintoma()
    {
        return $this->belongsTo(Sintoma::class, 'idsintoma', 'idsintoma');
    }
}
