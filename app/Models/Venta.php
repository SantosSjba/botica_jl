<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';

    protected $primaryKey = 'idventa';

    public $timestamps = false;

    protected $fillable = [
        'idconf', 'tipocomp', 'idcliente', 'idusuario', 'idserie', 'fecha_emision',
        'op_gravadas', 'op_exoneradas', 'op_inafectas', 'igv', 'total', 'estado', 'numope',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'op_gravadas' => 'decimal:2',
        'op_exoneradas' => 'decimal:2',
        'op_inafectas' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'idusuario', 'idusu');
    }
}
