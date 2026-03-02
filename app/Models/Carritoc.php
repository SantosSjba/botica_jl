<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carritoc extends Model
{
    protected $table = 'carritoc';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'idproducto', 'descripcion', 'presentacion', 'cantidad', 'precio', 'importe', 'session_id',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
        'importe' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idproducto', 'idproducto');
    }
}
