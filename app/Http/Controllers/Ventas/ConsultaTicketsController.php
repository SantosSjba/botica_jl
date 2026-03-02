<?php

namespace App\Http\Controllers\Ventas;

use App\Helpers\PermisosHelper;
use App\Http\Controllers\Controller;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Consulta de tickets (serie T001). Permite anular y devolver stock.
 */
class ConsultaTicketsController extends Controller
{
    protected array $sortColumns = [
        'idventa' => 'venta.idventa',
        'fecha_emision' => 'venta.fecha_emision',
        'serie' => 'serie.serie',
        'correlativo' => 'serie.correlativo',
        'estado' => 'venta.estado',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('pages.ventas.tickets._tabla-tickets', $data);
        }

        return view('pages.ventas.tickets.index', array_merge($data, [
            'title' => 'Consulta tickets',
        ]));
    }

    /** @return array<string, mixed> */
    protected function getData(Request $request): array
    {
        $fechaDesde = trim((string) $request->input('fecha_desde', ''));
        $fechaHasta = trim((string) $request->input('fecha_hasta', ''));
        $sort = $request->input('sort', 'idventa');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'idventa';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = Venta::query()
            ->join('serie', 'venta.idserie', '=', 'serie.idserie')
            ->where('serie.serie', '=', 'T001')
            ->with(['serie', 'cliente'])
            ->select('venta.*');

        if (!PermisosHelper::isAdministrador()) {
            $query->where('venta.idusuario', auth()->id());
        }

        if ($fechaDesde !== '') {
            $query->where('venta.fecha_emision', '>=', $fechaDesde . ' 00:00:00');
        }
        if ($fechaHasta !== '') {
            $query->where('venta.fecha_emision', '<=', $fechaHasta . ' 23:59:59');
        }

        $ventas = $query->orderBy($orderColumn, $direction)->paginate(15)->withQueryString();

        return [
            'ventas' => $ventas,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'sort' => $sort,
            'direction' => $direction,
        ];
    }

    /** Anular ticket y devolver stock. */
    public function anular(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');
        if ($id < 1) {
            return response()->json(['success' => false, 'message' => 'ID de venta no válido.']);
        }

        $venta = Venta::with('serie')->find($id);
        if (!$venta) {
            return response()->json(['success' => false, 'message' => 'Venta no encontrada.']);
        }
        if ($venta->serie && $venta->serie->serie !== 'T001') {
            return response()->json(['success' => false, 'message' => 'Solo se pueden anular tickets (serie T001).']);
        }
        if (($venta->estado ?? '') === 'anulado' || ($venta->feestado ?? '') === 'anulado') {
            return response()->json(['success' => false, 'message' => 'El ticket ya está anulado.']);
        }

        try {
            DB::beginTransaction();

            $detalles = DetalleVenta::where('idventa', $id)->get();
            foreach ($detalles as $d) {
                Producto::where('idproducto', $d->idproducto)->increment('stock', (int) $d->cantidad);
            }

            $data = ['estado' => 'anulado'];
            if (Schema::hasColumn($venta->getTable(), 'feestado')) {
                $data['feestado'] = 'anulado';
            }
            $venta->update($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ticket anulado. Stock devuelto.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json(['success' => false, 'message' => 'Error al anular: ' . $e->getMessage()]);
        }
    }
}
