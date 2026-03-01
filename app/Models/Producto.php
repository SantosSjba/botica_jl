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

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idcategoria', 'idcategoria');
    }

    public function laboratorio()
    {
        return $this->belongsTo(Cliente::class, 'idcliente', 'idcliente');
    }

    public function tipoAfectacion()
    {
        return $this->belongsTo(TipoAfectacion::class, 'idtipoaf', 'idtipoa');
    }

    /** No se puede eliminar si tiene ventas o compras en detalle. */
    public function puedeEliminar(): bool
    {
        if (\Illuminate\Support\Facades\DB::table('detalleventa')->where('idproducto', $this->idproducto)->exists()) {
            return false;
        }
        if (\Illuminate\Support\Facades\DB::table('detallecompra')->where('idproducto', $this->idproducto)->exists()) {
            return false;
        }
        return true;
    }

    public function mensajeNoEliminable(): string
    {
        $ventas = \Illuminate\Support\Facades\DB::table('detalleventa')->where('idproducto', $this->idproducto)->count();
        $compras = \Illuminate\Support\Facades\DB::table('detallecompra')->where('idproducto', $this->idproducto)->count();
        $partes = [];
        if ($ventas > 0) {
            $partes[] = $ventas . ' venta(s)';
        }
        if ($compras > 0) {
            $partes[] = $compras . ' compra(s)';
        }
        return 'No se puede eliminar el producto porque tiene ' . implode(' y ', $partes) . ' registrada(s). Puede desactivarlo en su lugar.';
    }
}
