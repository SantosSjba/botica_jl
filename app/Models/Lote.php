<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';

    protected $primaryKey = 'idlote';

    public $timestamps = false;

    protected $fillable = ['numero', 'fecha_vencimiento', 'idsucu_c'];

    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'idlote', 'idlote');
    }

    public function puedeEliminar(): bool
    {
        return !$this->productos()->exists();
    }

    public function mensajeNoEliminable(): string
    {
        return 'No se puede eliminar el lote porque tiene productos asociados.';
    }
}
