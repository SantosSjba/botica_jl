<?php

namespace App\Http\Controllers\Reportes;

use App\Helpers\PermisosHelper;
use App\Http\Controllers\Controller;
use App\Models\CajaApertura;
use App\Models\CajaCierre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CuadreCajaController extends Controller
{
    /**
     * Muestra el cuadre diario de caja para un usuario y fecha.
     * ADMIN puede ver cualquier usuario vía GET; USUARIO solo el propio.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $tipo = PermisosHelper::tipo();
        $usuCuadre = $user->usuario ?? '';
        $diaCuadre = now()->toDateString();

        if ($tipo === 'ADMINISTRADOR' && $request->has('usuario') && $request->has('fecha')) {
            $usuCuadre = trim((string) $request->input('usuario'));
            $fec = trim((string) $request->input('fecha'));
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fec) && strtotime($fec) !== false) {
                $diaCuadre = $fec;
            }
        } elseif ($request->has('fecha')) {
            $fec = trim((string) $request->input('fecha'));
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fec) && strtotime($fec) !== false) {
                $diaCuadre = $fec;
            }
        }

        $usuarioModel = \App\Models\Usuario::where('usuario', $usuCuadre)->first();
        if (!$usuarioModel) {
            abort(404, 'Usuario no encontrado.');
        }
        $idUsuario = $usuarioModel->idusu;

        $apertura = CajaApertura::where('usuario', $usuCuadre)
            ->where('fecha', $diaCuadre)
            ->orderByDesc('idcaja_a')
            ->first();
        if (!$apertura) {
            abort(404, 'No hay datos de apertura de caja para el usuario y fecha seleccionados.');
        }

        $porForma = DB::table('venta')
            ->whereBetween('fecha_emision', [$diaCuadre . ' 00:00:00', $diaCuadre . ' 23:59:59'])
            ->where('idusuario', $idUsuario)
            ->whereNotIn('estado', ['anulado'])
            ->selectRaw('formadepago, COALESCE(SUM(total), 0) as total')
            ->groupBy('formadepago')
            ->pluck('total', 'formadepago')
            ->map(fn ($v) => (float) $v)
            ->all();

        $totalVentas = array_sum($porForma);
        $cierre = CajaCierre::where('usuario', $usuCuadre)->where('fecha', $diaCuadre)->orderByDesc('idcaja_c')->first();

        return view('pages.reportes.cuadre-caja', [
            'usuario' => $usuCuadre,
            'fecha' => $diaCuadre,
            'apertura' => $apertura,
            'porForma' => $porForma,
            'totalVentas' => $totalVentas,
            'cierre' => $cierre,
        ]);
    }
}
