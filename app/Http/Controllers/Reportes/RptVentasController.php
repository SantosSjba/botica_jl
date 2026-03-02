<?php

namespace App\Http\Controllers\Reportes;

use App\Helpers\PermisosHelper;
use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RptVentasController extends Controller
{
    /**
     * Reporte de ventas por rango de fechas (Rpt. Ventas).
     * USUARIO: solo sus ventas. ADMINISTRADOR: todas.
     */
    public function ventasRango(Request $request): View
    {
        $primerDia = now()->startOfMonth()->toDateString();
        $ultimoDia = now()->endOfMonth()->toDateString();
        $desde = $request->input('desde', $primerDia);
        $hasta = $request->input('hasta', $ultimoDia);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde) || strtotime($desde) === false) {
            $desde = $primerDia;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta) || strtotime($hasta) === false) {
            $hasta = $ultimoDia;
        }
        if ($desde > $hasta) {
            $desde = $primerDia;
            $hasta = $ultimoDia;
        }

        $filas = $this->queryDetalleVentas($request, $desde, $hasta);
        $totalVentas = $filas->sum('importe_total');
        $config = Configuracion::first();
        $simboloMoneda = $config->simbolo_moneda ?? 'S/';

        return view('pages.reportes.ventas-rango', [
            'title' => 'Reporte de ventas',
            'desde' => $desde,
            'hasta' => $hasta,
            'filas' => $filas,
            'totalVentas' => $totalVentas,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }

    /**
     * Reporte de ventas del día (Rpt. Ventas del día).
     */
    public function ventasDia(Request $request): View
    {
        $dia = now()->toDateString();
        $fecha = $request->input('fecha', $dia);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || strtotime($fecha) === false) {
            $fecha = $dia;
        }

        $filas = $this->queryDetalleVentas($request, $fecha, $fecha);
        $totalVentas = $filas->sum('importe_total');
        $config = Configuracion::first();
        $simboloMoneda = $config->simbolo_moneda ?? 'S/';

        return view('pages.reportes.ventas-dia', [
            'title' => 'Reporte ventas del día',
            'fecha' => $fecha,
            'filas' => $filas,
            'totalVentas' => $totalVentas,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }

    /**
     * Query detalle de ventas (venta + detalleventa + productos), excluyendo anuladas.
     * USUARIO: solo idusuario del usuario actual.
     */
    protected function queryDetalleVentas(Request $request, string $desde, string $hasta)
    {
        $query = DB::table('venta as v')
            ->join('detalleventa as dv', 'v.idventa', '=', 'dv.idventa')
            ->join('productos as p', 'dv.idproducto', '=', 'p.idproducto')
            ->whereBetween('v.fecha_emision', [$desde, $hasta])
            ->whereNotIn('v.estado', ['anulado'])
            ->select(
                'v.idventa',
                'v.fecha_emision',
                'v.total',
                'dv.item',
                'dv.cantidad',
                'dv.precio_unitario',
                'dv.valor_unitario',
                'dv.importe_total',
                DB::raw('p.descripcion as producto')
            )
            ->orderBy('v.fecha_emision')
            ->orderBy('v.idventa')
            ->orderBy('dv.item');

        if (PermisosHelper::tipo() === 'USUARIO') {
            $idUsuario = $request->user()->idusu ?? $request->user()->getAuthIdentifier();
            $query->where('v.idusuario', $idUsuario);
        }

        return $query->get();
    }
}
