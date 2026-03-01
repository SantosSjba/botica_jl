<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    protected $table = 'presentacion';

    protected $primaryKey = 'idpresentacion';

    public $timestamps = false;

    protected $fillable = ['presentacion', 'idsucu_c'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'idpresentacion', 'idpresentacion');
    }

    public function puedeEliminar(): bool
    {
        return !$this->productos()->exists();
    }

    public function mensajeNoEliminable(): string
    {
        return 'No se puede eliminar la presentación porque tiene productos asociados.';
    }
}
