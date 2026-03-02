<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Models\NotaCredito;
use App\Models\Serie;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Nota de crédito: referencia a una venta (factura/boleta), genera nota con serie BN01/FN01.
 */
class NotaCreditoController extends Controller
{
    public function index(): View
    {
        return view('pages.ventas.nota-credito.index', [
            'title' => 'Nota de crédito',
        ]);
    }

    /** Buscar venta por serie y correlativo (comprobante de referencia). */
    public function buscarReferencia(Request $request): JsonResponse
    {
        $serieRef = $request->input('serie_ref', '');
        $correlRef = $request->input('correlativo_ref', '');
        if ($serieRef === '' || $correlRef === '') {
            return response()->json(['success' => false, 'message' => 'Indique serie y correlativo de referencia.']);
        }

        $venta = Venta::query()
            ->join('serie', 'venta.idserie', '=', 'serie.idserie')
            ->where('serie.serie', $serieRef)
            ->where('serie.correlativo', $correlRef)
            ->where('serie.serie', '!=', 'T001')
            ->with(['serie', 'cliente'])
            ->select('venta.*')
            ->first();

        if (!$venta) {
            return response()->json(['success' => false, 'message' => 'No se encontró comprobante con esa serie y correlativo.']);
        }
        if (($venta->estado ?? '') === 'anulado') {
            return response()->json(['success' => false, 'message' => 'Ese comprobante ya está anulado.']);
        }

        return response()->json([
            'success' => true,
            'idventa' => $venta->idventa,
            'serie_ref' => $venta->serie->serie ?? '',
            'correlativo_ref' => $venta->serie->correlativo ?? '',
            'cliente' => $venta->cliente->nombres ?? '—',
            'total' => round((float) $venta->total, 2),
            'op_gravadas' => round((float) $venta->op_gravadas, 2),
            'op_exoneradas' => round((float) $venta->op_exoneradas, 2),
            'op_inafectas' => round((float) $venta->op_inafectas, 2),
            'igv' => round((float) $venta->igv, 2),
        ]);
    }

    /** Siguiente correlativo para serie de nota (BN01 o FN01). */
    public function siguienteCorrelativo(Request $request): JsonResponse
    {
        $serieN = $request->input('serie_n', '');
        if ($serieN === '') {
            return response()->json(['success' => false, 'correlativo' => null]);
        }
        $max = Serie::where('serie', $serieN)->max('correlativo');
        $siguiente = $max ? (int) $max + 1 : 11;
        return response()->json(['success' => true, 'correlativo' => $siguiente]);
    }

    /** Registrar nota de crédito. */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idventa' => 'required|integer|min:1',
            'fecha_emision' => 'required|date',
            'serie_ref' => 'required|string|max:20',
            'correlativo_ref' => 'required|string|max:20',
            'serie_n' => 'required|string|in:BN01,FN01',
            'correlativo_n' => 'required|integer|min:1',
        ], [
            'idventa.required' => 'Debe buscar y seleccionar el comprobante de referencia.',
            'serie_n.in' => 'Serie de nota debe ser BN01 o FN01.',
        ]);

        $venta = Venta::with('serie')->find($validated['idventa']);
        if (!$venta) {
            return response()->json(['success' => false, 'message' => 'Venta de referencia no encontrada.']);
        }
        if ($venta->serie && $venta->serie->serie === 'T001') {
            return response()->json(['success' => false, 'message' => 'No se puede emitir nota de crédito para un ticket.']);
        }
        if (($venta->estado ?? '') === 'anulado') {
            return response()->json(['success' => false, 'message' => 'El comprobante ya está anulado.']);
        }

        try {
            DB::beginTransaction();

            $serie = Serie::create([
                'tipocomp' => '07',
                'serie' => $validated['serie_n'],
                'correlativo' => $validated['correlativo_n'],
            ]);

            $idconf = \App\Models\Configuracion::first()?->idconfi ?? 1;
            NotaCredito::create([
                'idconf' => $idconf,
                'tipocomp' => '07',
                'idcliente' => $venta->idcliente,
                'idusuario' => auth()->id(),
                'idserie' => $serie->idserie,
                'fecha_emision' => $validated['fecha_emision'],
                'op_gravadas' => $venta->op_gravadas,
                'op_exoneradas' => $venta->op_exoneradas,
                'op_inafectas' => $venta->op_inafectas,
                'igv' => $venta->igv,
                'total' => $venta->total,
                'serie_ref' => $validated['serie_ref'],
                'correlativo_ref' => $validated['correlativo_ref'],
                'codmotivo' => '01',
                'feestado' => 'registrado',
                'idventa' => $venta->idventa,
            ]);

            $venta->update(['estado' => 'anulado']);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Nota de crédito registrada. Comprobante de referencia anulado.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()]);
        }
    }
}
