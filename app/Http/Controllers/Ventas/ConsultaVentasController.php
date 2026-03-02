<?php

namespace App\Http\Controllers\Ventas;

use App\Helpers\PermisosHelper;
use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Consulta de ventas (facturas y boletas, serie distinta de T001).
 * ADMIN ve todas; USUARIO solo las propias.
 */
class ConsultaVentasController extends Controller
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
            return view('pages.ventas.consulta._tabla-ventas', $data);
        }

        return view('pages.ventas.consulta.index', array_merge($data, [
            'title' => 'Consulta ventas',
        ]));
    }

    /** @return array<string, mixed> */
    protected function getData(Request $request): array
    {
        $fechaDesde = $request->input('fecha_desde', '');
        $fechaHasta = $request->input('fecha_hasta', '');
        $sort = $request->input('sort', 'idventa');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'idventa';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = Venta::query()
            ->join('serie', 'venta.idserie', '=', 'serie.idserie')
            ->where('serie.serie', '!=', 'T001')
            ->with(['serie', 'cliente', 'usuario'])
            ->select('venta.*');

        if (!PermisosHelper::isAdministrador()) {
            $query->where('venta.idusuario', auth()->id());
        }

        if ($fechaDesde !== '') {
            $query->whereDate('venta.fecha_emision', '>=', $fechaDesde);
        }
        if ($fechaHasta !== '') {
            $query->whereDate('venta.fecha_emision', '<=', $fechaHasta);
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

    public function show(int $id): View
    {
        $venta = Venta::with(['serie', 'cliente', 'usuario', 'detalles.producto.presentacion'])->findOrFail($id);

        if ($venta->serie && $venta->serie->serie === 'T001') {
            abort(404, 'Esta venta es un ticket. Use Consulta tickets.');
        }

        $config = \App\Models\Configuracion::first();
        $simboloMoneda = $config?->simbolo_moneda ?? 'S/';

        return view('pages.ventas.consulta.show', [
            'title' => 'Venta #' . $venta->idventa,
            'venta' => $venta,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }
}
