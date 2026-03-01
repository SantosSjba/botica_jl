<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use App\Models\Configuracion;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = auth()->user();
        $usu = $usuario->usuario;
        $hoy = now()->toDateString();

        // Razón social y símbolo moneda
        $config = Configuracion::first();
        $razonSocial = $config?->razon_social ?? '';
        $simboloMoneda = $config?->simbolo_moneda ?? 'S/';

        // Monto apertura caja del día (usuario actual)
        $cajaHoy = CajaApertura::where('usuario', $usu)
            ->where('fecha', $hoy)
            ->orderByDesc('idcaja_a')
            ->first();
        $montoCaja = $cajaHoy ? (float) $cajaHoy->monto : 0;

        // Filtros resumen financiero
        $primerDiaMes = now()->startOfMonth()->toDateString();
        $ultimoDiaMes = now()->endOfMonth()->toDateString();
        $fechaDesde = $request->input('fecha_desde', $primerDiaMes);
        $fechaHasta = $request->input('fecha_hasta', $ultimoDiaMes);
        $filtroUsuario = (int) $request->input('filtro_usuario', 0);

        if (!strtotime($fechaDesde) || !strtotime($fechaHasta)) {
            $fechaDesde = $primerDiaMes;
            $fechaHasta = $ultimoDiaMes;
        }
        if ($fechaDesde > $fechaHasta) {
            $fechaDesde = $primerDiaMes;
            $fechaHasta = $ultimoDiaMes;
        }

        // Lista usuarios activos para el filtro
        $listaUsuarios = Usuario::where('estado', 'Activo')
            ->orderBy('nombres')
            ->get(['idusu', 'usuario', 'nombres']);

        // Resumen financiero (solo si hay usuarios y ventas)
        $ventas = 0;
        $costos = 0;
        $ganancia = 0;
        $gastos = 0;
        $neto = 0;

        $queryVentas = Venta::whereBetween('fecha_emision', [$fechaDesde, $fechaHasta])
            ->where('estado', '!=', 'anulado');
        if ($filtroUsuario > 0) {
            $queryVentas->where('idusuario', $filtroUsuario);
        }
        $ventas = (float) $queryVentas->sum('total');

        $queryCostos = DB::table('detalleventa')
            ->join('productos', 'detalleventa.idproducto', '=', 'productos.idproducto')
            ->join('venta', 'detalleventa.idventa', '=', 'venta.idventa')
            ->whereBetween('venta.fecha_emision', [$fechaDesde, $fechaHasta])
            ->where('venta.estado', '!=', 'anulado');
        if ($filtroUsuario > 0) {
            $queryCostos->where('venta.idusuario', $filtroUsuario);
        }
        $costos = (float) $queryCostos->selectRaw(
            'COALESCE(SUM(LEAST(productos.precio_compra * detalleventa.cantidad, detalleventa.importe_total)), 0) as total'
        )->value('total');

        $ganancia = $ventas - $costos;
        $neto = $ganancia - $gastos;

        // Productos por vencer (14 días) o vencidos
        $productosPorVencer = Producto::query()
            ->join('lote', 'productos.idlote', '=', 'lote.idlote')
            ->whereRaw('DATE_SUB(lote.fecha_vencimiento, INTERVAL 14 DAY) <= CURDATE()')
            ->select('productos.*', 'lote.fecha_vencimiento')
            ->orderBy('lote.fecha_vencimiento')
            ->get();

        // Productos con bajo stock (stock <= stockminimo, con mínimo 1 para no vacío)
        $productosBajoStock = Producto::query()
            ->join('lote', 'productos.idlote', '=', 'lote.idlote')
            ->whereRaw('productos.stock <= GREATEST(productos.stockminimo, 1)')
            ->select('productos.*', 'lote.fecha_vencimiento')
            ->orderBy('productos.stock')
            ->get();

        return view('pages.dashboard.inicio', [
            'title' => 'Inicio',
            'usuario' => $usu,
            'razonSocial' => $razonSocial,
            'montoCaja' => $montoCaja,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'filtroUsuario' => $filtroUsuario,
            'listaUsuarios' => $listaUsuarios,
            'ventas' => $ventas,
            'costos' => $costos,
            'ganancia' => $ganancia,
            'gastos' => $gastos,
            'neto' => $neto,
            'productosPorVencer' => $productosPorVencer,
            'productosBajoStock' => $productosBajoStock,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }
}
