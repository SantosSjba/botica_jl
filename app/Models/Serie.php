<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Serie de comprobantes (ticket, factura, boleta). tipocomp: 00=TICKET, 01=FACTURA, 03=BOLETA.
 */
class Serie extends Model
{
    protected $table = 'serie';

    protected $primaryKey = 'idserie';

    public $timestamps = false;

    protected $fillable = ['idserie', 'tipocomp', 'serie', 'correlativo'];

    protected $casts = [
        'correlativo' => 'integer',
    ];
}
