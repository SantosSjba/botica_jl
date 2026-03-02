<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultaComprasController extends Controller
{
    /** Columnas permitidas para ordenar */
    protected array $sortColumns = [
        'idcompra' => 'compra.idcompra',
        'fecha' => 'compra.fecha',
        'docu' => 'compra.docu',
        'num_docu' => 'compra.num_docu',
        'total' => 'compra.total',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('pages.compras.consulta._tabla-compras', $data);
        }

        return view('pages.compras.consulta.index', array_merge($data, [
            'title' => 'Consulta compras',
        ]));
    }

    /** @return array<string, mixed> */
    protected function getData(Request $request): array
    {
        $buscar = $request->input('buscar', '');
        $fechaDesde = $request->input('fecha_desde', '');
        $fechaHasta = $request->input('fecha_hasta', '');
        $docu = $request->input('docu', '');
        $sort = $request->input('sort', 'idcompra');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'idcompra';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = Compra::query()->with('proveedor');

        if ($fechaDesde !== '') {
            $query->whereDate('fecha', '>=', $fechaDesde);
        }
        if ($fechaHasta !== '') {
            $query->whereDate('fecha', '<=', $fechaHasta);
        }
        if ($docu !== '') {
            $query->where('docu', $docu);
        }
        if ($buscar !== '') {
            $term = '%' . trim($buscar) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('num_docu', 'like', $term)
                    ->orWhereHas('proveedor', function ($q2) use ($term) {
                        $q2->where('nombres', 'like', $term);
                    });
            });
        }

        $compras = $query->orderBy($orderColumn, $direction)->paginate(15)->withQueryString();

        return [
            'compras' => $compras,
            'buscar' => $buscar,
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'docu' => $docu,
            'sort' => $sort,
            'direction' => $direction,
        ];
    }

    /**
     * Ver detalle de una compra (y opción imprimir).
     */
    public function show(int $id): View
    {
        $compra = Compra::with(['proveedor', 'detalle.producto.presentacion'])->findOrFail($id);

        return view('pages.compras.consulta.show', [
            'title' => 'Compra #' . $compra->idcompra,
            'compra' => $compra,
        ]);
    }
}
