<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';

    protected $primaryKey = 'idventa';

    public $timestamps = false;

    protected $fillable = [
        'idventa', 'idconf', 'tipocomp', 'idcliente', 'idusuario', 'idserie', 'fecha_emision',
        'op_gravadas', 'op_exoneradas', 'op_inafectas', 'igv', 'total', 'estado', 'feestado', 'numope',
        'formadepago', 'efectivo', 'vuelto', 'nombrexml', 'femensajesunat',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'op_gravadas' => 'decimal:2',
        'op_exoneradas' => 'decimal:2',
        'op_inafectas' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'efectivo' => 'decimal:2',
        'vuelto' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idusuario', 'idusu');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idcliente', 'idcliente');
    }

    public function serie()
    {
        return $this->belongsTo(Serie::class, 'idserie', 'idserie');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'idventa', 'idventa');
    }
}
