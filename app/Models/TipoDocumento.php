<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documento';

    protected $primaryKey = 'idtipo_docu';

    public $timestamps = false;

    protected $fillable = ['codigo', 'descripcion'];

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'id_tipo_docu', 'idtipo_docu');
    }
}
