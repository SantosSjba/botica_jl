<?php

namespace App\Http\Controllers\Caja;

use App\Helpers\PermisosHelper;
use App\Http\Controllers\Controller;
use App\Models\CajaApertura;
use App\Models\CajaCierre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CajaController extends Controller
{
    /** Opciones de caja y turno (igual que sistema antiguo) */
    public const CAJAS = ['caja 1', 'caja 2'];
    public const TURNOS = ['mañana', 'tarde', 'noche', 'completo'];

    /**
     * Formulario de apertura de caja.
     * Solo accesible si no hay caja abierta.
     */
    public function apertura(Request $request): View|RedirectResponse
    {
        if (PermisosHelper::tieneCajaAbierta()) {
            return redirect()->route('caja.seguimiento')
                ->with('error', __('No puede aperturar otra caja mientras tenga una caja abierta.'));
        }

        $user = $request->user();
        $hoy = now()->toDateString();
        $hora = now()->format('g:i-a');

        return view('pages.caja.apertura', [
            'title' => 'Apertura de caja',
            'usuario' => $user->usuario ?? $user->nombre ?? '',
            'fecha' => $hoy,
            'hora' => $hora,
            'cajas' => self::CAJAS,
            'turnos' => self::TURNOS,
        ]);
    }

    /**
     * Registrar apertura de caja.
     */
    public function storeApertura(Request $request): RedirectResponse
    {
        if (PermisosHelper::tieneCajaAbierta()) {
            return redirect()->route('caja.seguimiento')
                ->with('error', __('Ya tiene una caja abierta.'));
        }

        $validated = $request->validate([
            'txtcaja' => 'required|string|in:' . implode(',', self::CAJAS),
            'txtturno' => 'required|string|in:' . implode(',', self::TURNOS),
            'txtmon' => 'required|numeric|min:0',
        ], [], [
            'txtcaja' => 'caja',
            'txtturno' => 'turno',
            'txtmon' => 'monto de apertura',
        ]);

        $user = $request->user();
        $hoy = now()->toDateString();
        $hora = now()->format('g:i-a');

        CajaApertura::create([
            'fecha' => $hoy,
            'caja' => $validated['txtcaja'],
            'turno' => $validated['txtturno'],
            'hora' => $hora,
            'monto' => round((float) $validated['txtmon'], 2),
            'usuario' => $user->usuario,
            'estado' => 'Abierto',
        ]);

        return redirect()->route('dashboard')
            ->with('success', __('Caja aperturada correctamente.'));
    }

    /**
     * Obtiene la caja abierta del usuario actual (cualquier día).
     */
    protected function getCajaAbierta(string $usuarioLogin): ?CajaApertura
    {
        $abierta = CajaApertura::where('usuario', $usuarioLogin)
            ->where('estado', 'Abierto')
            ->orderByDesc('fecha')
            ->orderByDesc('idcaja_a')
            ->first();
        if ($abierta) {
            return $abierta;
        }
        $hoy = now()->toDateString();
        return CajaApertura::where('usuario', $usuarioLogin)
            ->where('fecha', $hoy)
            ->orderByDesc('idcaja_a')
            ->first();
    }

    /**
     * Formulario de cierre de caja.
     * Solo accesible si hay caja abierta.
     */
    public function cierre(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $usuarioLogin = $user->usuario ?? '';

        $cajaAbierta = $this->getCajaAbierta($usuarioLogin);
        if (!$cajaAbierta || $cajaAbierta->estado !== 'Abierto') {
            return redirect()->route('caja.apertura')
                ->with('error', __('No tiene una caja abierta. Debe aperturar caja primero.'));
        }

        $fechaCaja = $cajaAbierta->fecha->format('Y-m-d');
        $idUsuario = $user->idusu ?? $user->getAuthIdentifier();

        // Totales por forma de pago (ventas no anuladas del día de la caja y del cajero)
        $porForma = DB::table('venta')
            ->where('fecha_emision', $fechaCaja)
            ->where('idusuario', $idUsuario)
            ->whereNotIn('estado', ['anulado'])
            ->selectRaw('formadepago, COALESCE(SUM(total), 0) as total')
            ->groupBy('formadepago')
            ->pluck('total', 'formadepago')
            ->map(fn ($v) => (float) $v)
            ->all();

        $formaEfectivo = $porForma['EFECTIVO'] ?? 0;
        $formaTarjeta = $porForma['TARJETA'] ?? 0;
        $formaDeposito = $porForma['DEPOSITO EN CUENTA'] ?? 0;
        $totalVentas = array_sum($porForma);
        $cajaSistema = $totalVentas + (float) $cajaAbierta->monto;
        $hora = now()->format('g:i-a');

        return view('pages.caja.cierre', [
            'title' => 'Cierre de caja',
            'cajaApertura' => $cajaAbierta,
            'usuario' => $usuarioLogin,
            'fechaCaja' => $fechaCaja,
            'hora' => $hora,
            'porForma' => $porForma,
            'formaEfectivo' => $formaEfectivo,
            'formaTarjeta' => $formaTarjeta,
            'formaDeposito' => $formaDeposito,
            'totalVentas' => $totalVentas,
            'cajaSistema' => $cajaSistema,
            'diaActual' => now()->toDateString(),
        ]);
    }

    /**
     * Registrar cierre de caja.
     */
    public function storeCierre(Request $request): RedirectResponse
    {
        $user = $request->user();
        $usuarioLogin = $user->usuario ?? '';

        $cajaAbierta = $this->getCajaAbierta($usuarioLogin);
        if (!$cajaAbierta || $cajaAbierta->estado !== 'Abierto') {
            return redirect()->route('caja.apertura')
                ->with('error', __('No tiene una caja abierta.'));
        }

        $validated = $request->validate([
            'idcaja_a' => 'required|integer|exists:caja_apertura,idcaja_a',
            'txtefe' => 'required|numeric|min:0',
        ], [], [
            'txtefe' => 'efectivo en caja',
        ]);

        if ((int) $cajaAbierta->idcaja_a !== (int) $validated['idcaja_a']) {
            return redirect()->route('caja.cierre')
                ->with('error', __('La caja a cerrar no coincide. Recargue la página.'));
        }

        $fechaCaja = $cajaAbierta->fecha->format('Y-m-d');
        $totalVentas = (float) $request->input('txttot', 0);
        $montoA = (float) $cajaAbierta->monto;
        $cajaSistema = (float) $request->input('txtsis', $totalVentas + $montoA);
        $efectivoCaja = (float) $validated['txtefe'];
        $diferencia = round($cajaSistema - $efectivoCaja, 2);
        $pagosEfectivo = (float) $request->input('txtp_e', 0);
        $pagosTarjeta = (float) $request->input('txt_t', 0);
        $pagosDeposito = (float) $request->input('txtp_d', 0);

        CajaCierre::create([
            'fecha' => $fechaCaja,
            'caja' => $cajaAbierta->caja,
            'turno' => $cajaAbierta->turno,
            'hora' => now()->format('g:i-a'),
            'usuario' => $usuarioLogin,
            'pagos_efectivo' => $pagosEfectivo,
            'pagos_tarjeta' => $pagosTarjeta,
            'pagos_deposito' => $pagosDeposito,
            'total_venta' => $totalVentas,
            'monto_a' => $montoA,
            'caja_sistema' => $cajaSistema,
            'efectivo_caja' => $efectivoCaja,
            'diferencia' => $diferencia,
        ]);

        $cajaAbierta->update(['estado' => 'Cerrado']);

        return redirect()->route('caja.seguimiento')
            ->with('success', __('Caja cerrada correctamente.'));
    }

    /**
     * Seguimiento de caja: listado de aperturas con filtros.
     * ADMIN: todas las cajas por fecha y usuario. USUARIO: solo las propias.
     */
    public function seguimiento(Request $request): View|RedirectResponse
    {
        if (!PermisosHelper::puedeVerCajaSeguimiento()) {
            return redirect()->route('dashboard')->with('error', __('No tiene permiso para ver el seguimiento de caja.'));
        }
        $user = $request->user();
        $tipo = PermisosHelper::tipo();
        $dia = now()->toDateString();

        $filtroFecha = $request->input('filtro_fecha', $dia);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filtroFecha) || strtotime($filtroFecha) === false) {
            $filtroFecha = $dia;
        }
        $filtroUsuario = $request->input('filtro_usuario', '');

        $query = CajaApertura::where('fecha', $filtroFecha)
            ->orderBy('usuario')
            ->orderByDesc('hora');

        if ($tipo !== 'ADMINISTRADOR') {
            $query->where('usuario', $user->usuario);
        } elseif ($filtroUsuario !== '') {
            $query->where('usuario', $filtroUsuario);
        }

        $aperturas = $query->get();
        $listaUsuarios = $tipo === 'ADMINISTRADOR'
            ? CajaApertura::distinct()->pluck('usuario')->sort()->values()
            : collect();

        $data = [
            'aperturas' => $aperturas,
            'listaUsuarios' => $listaUsuarios,
            'filtroFecha' => $filtroFecha,
            'filtroUsuario' => $filtroUsuario,
            'esAdministrador' => $tipo === 'ADMINISTRADOR',
        ];

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('pages.caja._tabla-seguimiento', $data);
        }

        return view('pages.caja.seguimiento', array_merge($data, [
            'title' => 'Seguimiento de caja',
        ]));
    }
}
