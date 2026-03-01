<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConsultaProductosController extends Controller
{
    /** Columnas permitidas para ordenar (request => tabla.columna o alias) */
    protected array $sortColumns = [
        'codigo' => 'productos.codigo',
        'descripcion' => 'productos.descripcion',
        'presentacion' => 'presentacion.presentacion',
        'fecha_vencimiento' => 'lote.fecha_vencimiento',
        'stock' => 'productos.stock',
        'precio_venta' => 'productos.precio_venta',
        'tipo' => 'productos.tipo',
        'estado' => 'productos.estado',
        'sintoma' => 'sintoma.sintoma',
        'lote' => 'lote.numero',
        'descuento' => 'productos.descuento',
        'ventasujeta' => 'productos.ventasujeta',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);
        $ajax = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($ajax) {
            return view('pages.consulta._tabla-productos', $data);
        }

        return view('pages.consulta.productos', array_merge($data, [
            'title' => 'Consultas - Productos farmacéuticos',
        ]));
    }

    /** @return array<string, mixed> */
    protected function getData(Request $request): array
    {
        $buscar = $request->input('buscar', '');
        $sort = $request->input('sort', 'codigo');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'codigo';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = DB::table('productos')
            ->join('lote', 'productos.idlote', '=', 'lote.idlote')
            ->join('presentacion', 'productos.idpresentacion', '=', 'presentacion.idpresentacion')
            ->join('sintoma', 'productos.idsintoma', '=', 'sintoma.idsintoma')
            ->select(
                'productos.idproducto',
                'productos.codigo',
                'productos.descripcion',
                'productos.tipo',
                'productos.stock',
                'productos.stockminimo',
                'productos.precio_venta',
                'productos.descuento',
                'productos.ventasujeta',
                'productos.estado',
                'lote.fecha_vencimiento',
                'lote.numero as lote_numero',
                'presentacion.presentacion as presentacion_nombre',
                'sintoma.sintoma as sintoma_nombre'
            );

        if ($buscar !== '') {
            $palabras = array_filter(preg_split('/\s+/', trim($buscar), -1, PREG_SPLIT_NO_EMPTY));
            foreach ($palabras as $palabra) {
                $term = '%' . $palabra . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('productos.codigo', 'like', $term)
                        ->orWhere('productos.descripcion', 'like', $term)
                        ->orWhere('presentacion.presentacion', 'like', $term)
                        ->orWhere('sintoma.sintoma', 'like', $term)
                        ->orWhere('lote.numero', 'like', $term);
                });
            }
        }

        $productos = $query->orderBy($orderColumn, $direction)->paginate(15)->withQueryString();
        $simboloMoneda = Configuracion::first()?->simbolo_moneda ?? 'S/';

        return [
            'productos' => $productos,
            'sort' => $sort,
            'direction' => $direction,
            'buscar' => $buscar,
            'simboloMoneda' => $simboloMoneda,
        ];
    }
}
