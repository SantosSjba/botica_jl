<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categoria';

    protected $primaryKey = 'idcategoria';

    public $timestamps = false;

    protected $fillable = ['forma_farmaceutica', 'ff_simplificada'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'idcategoria', 'idcategoria');
    }

    /** No se puede eliminar si hay productos que usan esta categoría. */
    public function puedeEliminar(): bool
    {
        return !$this->productos()->exists();
    }

    public function mensajeNoEliminable(): string
    {
        return 'No se puede eliminar la forma farmacéutica porque tiene productos asociados.';
    }
}
