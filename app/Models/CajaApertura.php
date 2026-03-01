<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaApertura extends Model
{
    protected $table = 'caja_apertura';

    protected $primaryKey = 'idcaja_a';

    public $timestamps = false;

    protected $fillable = ['fecha', 'caja', 'turno', 'hora', 'monto', 'usuario', 'estado'];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];
}
