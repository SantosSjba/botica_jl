<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $primaryKey = 'idconfi';

    public $timestamps = false;

    protected $fillable = [
        'logo', 'tipodoc', 'ruc', 'razon_social', 'nombre_comercial', 'direccion',
        'pais', 'departamento', 'provincia', 'distrito', 'ubigeo', 'usuario_sol',
        'clave_sol', 'simbolo_moneda', 'impuesto',
    ];
}
