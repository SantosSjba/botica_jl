<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sintoma extends Model
{
    protected $table = 'sintoma';

    protected $primaryKey = 'idsintoma';

    public $timestamps = false;

    protected $fillable = ['sintoma', 'idsucu_c'];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'idsintoma', 'idsintoma');
    }

    public function puedeEliminar(): bool
    {
        return !$this->productos()->exists();
    }

    public function mensajeNoEliminable(): string
    {
        return 'No se puede eliminar el síntoma porque tiene productos asociados.';
    }
}
