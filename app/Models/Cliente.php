<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';

    protected $primaryKey = 'idcliente';

    public $timestamps = false;

    protected $fillable = ['nombres', 'id_tipo_docu', 'direccion', 'nrodoc', 'tipo'];

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_docu', 'idtipo_docu');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, 'idcliente', 'idcliente');
    }

    /** No se puede eliminar si tiene productos, ventas, compras o notas de crédito asociados. */
    public function puedeEliminar(): bool
    {
        if ($this->productos()->exists()) {
            return false;
        }
        if (\Illuminate\Support\Facades\DB::table('venta')->where('idcliente', $this->idcliente)->exists()) {
            return false;
        }
        if (\Illuminate\Support\Facades\DB::table('compra')->where('idcliente', $this->idcliente)->exists()) {
            return false;
        }
        if (\Illuminate\Support\Facades\DB::table('nota_credito')->where('idcliente', $this->idcliente)->exists()) {
            return false;
        }
        return true;
    }

    /** Mensaje de error cuando no se puede eliminar. */
    public function mensajeNoEliminable(): string
    {
        $partes = [];
        if ($this->productos()->exists()) {
            $partes[] = $this->productos()->count() . ' producto(s) asociado(s)';
        }
        $ventas = \Illuminate\Support\Facades\DB::table('venta')->where('idcliente', $this->idcliente)->count();
        if ($ventas > 0) {
            $partes[] = $ventas . ' venta(s)';
        }
        $compras = \Illuminate\Support\Facades\DB::table('compra')->where('idcliente', $this->idcliente)->count();
        if ($compras > 0) {
            $partes[] = $compras . ' compra(s)';
        }
        $notas = \Illuminate\Support\Facades\DB::table('nota_credito')->where('idcliente', $this->idcliente)->count();
        if ($notas > 0) {
            $partes[] = $notas . ' nota(s) de crédito';
        }
        $tipo = $this->tipo === 'laboratorio' ? 'el laboratorio' : 'el cliente';
        return "No se puede eliminar {$tipo} \"{$this->nombres}\" porque tiene: " . implode(', ', $partes) . '.';
    }
}
