<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    protected $table = 'nota_credito';

    protected $primaryKey = 'idnota';

    public $timestamps = false;

    protected $fillable = [
        'idconf', 'tipocomp', 'idcliente', 'idusuario', 'idserie', 'fecha_emision',
        'op_gravadas', 'op_exoneradas', 'op_inafectas', 'igv', 'total',
        'serie_ref', 'correlativo_ref', 'codmotivo', 'feestado', 'idventa',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'op_gravadas' => 'decimal:2',
        'op_exoneradas' => 'decimal:2',
        'op_inafectas' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idventa', 'idventa');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'idcliente', 'idcliente');
    }
}
