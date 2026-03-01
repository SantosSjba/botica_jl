<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAfectacion extends Model
{
    protected $table = 'tipo_afectacion';

    protected $primaryKey = 'idtipoa';

    public $timestamps = false;

    protected $fillable = ['codigo', 'descripcion', 'codigo_afectacion', 'nombre_afectacion', 'tipo_afectacion'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'idtipoaf', 'idtipoa');
    }
}
