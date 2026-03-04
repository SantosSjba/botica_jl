<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Reporte de ingresos por tipo de pago (EFECTIVO, YAPE, TARJETA, etc.).
 * Usa la tabla pago_venta para ventas con múltiples pagos y ventas con un solo pago registrado.
 */
class RptIngresosPorPagoController extends Controller
{
    public function index(Request $request): View
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
            [$desde, $hasta] = [$hasta, $desde];
        }

        $porTipo = [];
        $totalGeneral = 0;

        if (Schema::hasTable('pago_venta')) {
            $porTipo = DB::table('pago_venta')
                ->join('venta', 'pago_venta.idventa', '=', 'venta.idventa')
                ->whereBetween('venta.fecha_emision', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
                ->whereNotIn('venta.estado', ['anulado'])
                ->selectRaw('pago_venta.tipo_pago, COALESCE(SUM(pago_venta.monto), 0) as total')
                ->groupBy('pago_venta.tipo_pago')
                ->orderBy('pago_venta.tipo_pago')
                ->pluck('total', 'tipo_pago')
                ->map(fn ($v) => (float) $v)
                ->all();
            $totalGeneral = array_sum($porTipo);
        } else {
            $porTipo = DB::table('venta')
                ->whereBetween('fecha_emision', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
                ->whereNotIn('estado', ['anulado'])
                ->selectRaw('formadepago as tipo_pago, COALESCE(SUM(total), 0) as total')
                ->groupBy('formadepago')
                ->orderBy('formadepago')
                ->pluck('total', 'tipo_pago')
                ->map(fn ($v) => (float) $v)
                ->all();
            $totalGeneral = array_sum($porTipo);
        }

        $config = Configuracion::first();
        $simboloMoneda = $config->simbolo_moneda ?? 'S/';

        return view('pages.reportes.ingresos-por-pago', [
            'title' => 'Ingresos por tipo de pago',
            'desde' => $desde,
            'hasta' => $hasta,
            'porTipo' => $porTipo,
            'totalGeneral' => $totalGeneral,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }
}
