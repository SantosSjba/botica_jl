<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RptComprasController extends Controller
{
    /**
     * Reporte de compras por rango de fechas (Rpt. Compras).
     */
    public function comprasRango(Request $request): View
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

        $compras = Compra::whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->orderBy('idcompra')
            ->get();

        $totalCompras = $compras->sum('total');
        $config = Configuracion::first();
        $simboloMoneda = $config->simbolo_moneda ?? 'S/';

        return view('pages.reportes.compras-rango', [
            'title' => 'Reporte de compras',
            'desde' => $desde,
            'hasta' => $hasta,
            'compras' => $compras,
            'totalCompras' => $totalCompras,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }

    /**
     * Reporte de compras del día (Rpt. Compras del día).
     */
    public function comprasDia(Request $request): View
    {
        $dia = now()->toDateString();
        $fecha = $request->input('fecha', $dia);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || strtotime($fecha) === false) {
            $fecha = $dia;
        }

        $compras = Compra::where('fecha', $fecha)
            ->orderBy('idcompra')
            ->get();

        $totalCompras = $compras->sum('total');
        $config = Configuracion::first();
        $simboloMoneda = $config->simbolo_moneda ?? 'S/';

        return view('pages.reportes.compras-dia', [
            'title' => 'Reporte compras del día',
            'fecha' => $fecha,
            'compras' => $compras,
            'totalCompras' => $totalCompras,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }
}
