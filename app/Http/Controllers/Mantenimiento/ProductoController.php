<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Presentacion;
use App\Models\Producto;
use App\Models\Sintoma;
use App\Models\Configuracion;
use App\Models\TipoAfectacion;
use App\Exports\ProductosExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductoController extends Controller
{
    /** Columnas permitidas para ordenar */
    protected array $sortColumns = [
        'codigo' => 'productos.codigo',
        'descripcion' => 'productos.descripcion',
        'presentacion' => 'presentacion.presentacion',
        'stock' => 'productos.stock',
        'precio_venta' => 'productos.precio_venta',
        'estado' => 'productos.estado',
        'tipo' => 'productos.tipo',
    ];

    public function index(Request $request): View
    {
        $data = $this->getData($request);
        $ajax = $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if ($ajax) {
            return view('pages.mantenimiento.productos._tabla-productos', $data);
        }

        return view('pages.mantenimiento.productos.index', array_merge($data, [
            'title' => 'Mantenimiento - Producto',
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

        $query = Producto::query()
            ->join('presentacion', 'productos.idpresentacion', '=', 'presentacion.idpresentacion')
            ->select('productos.*', 'presentacion.presentacion as presentacion_nombre');

        if ($buscar !== '') {
            $term = '%' . trim($buscar) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('productos.codigo', 'like', $term)
                    ->orWhere('productos.descripcion', 'like', $term)
                    ->orWhere('presentacion.presentacion', 'like', $term)
                    ->orWhere('productos.tipo', 'like', $term);
            });
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

    /**
     * Misma consulta que el listado (filtros y orden) sin paginar, limitada para exportación.
     */
    protected function getProductosParaExportar(Request $request, int $limit = 5000): \Illuminate\Database\Eloquent\Collection
    {
        $buscar = $request->input('buscar', '');
        $sort = $request->input('sort', 'codigo');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        if (!array_key_exists($sort, $this->sortColumns)) {
            $sort = 'codigo';
        }
        $orderColumn = $this->sortColumns[$sort];

        $query = Producto::query()
            ->join('presentacion', 'productos.idpresentacion', '=', 'presentacion.idpresentacion')
            ->select('productos.*', 'presentacion.presentacion as presentacion_nombre');

        if ($buscar !== '') {
            $term = '%' . trim($buscar) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('productos.codigo', 'like', $term)
                    ->orWhere('productos.descripcion', 'like', $term)
                    ->orWhere('presentacion.presentacion', 'like', $term)
                    ->orWhere('productos.tipo', 'like', $term);
            });
        }

        return $query->orderBy($orderColumn, $direction)->limit($limit)->get();
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $productos = $this->getProductosParaExportar($request);
        $simboloMoneda = Configuracion::first()?->simbolo_moneda ?? 'S/';

        $filename = 'productos_' . date('Y-m-d_His') . '.xlsx';
        $export = new ProductosExport($productos, $simboloMoneda);

        return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportPdf(Request $request): Response
    {
        $productos = $this->getProductosParaExportar($request);
        $simboloMoneda = Configuracion::first()?->simbolo_moneda ?? 'S/';

        $pdf = Pdf::loadView('pages.mantenimiento.productos.export-pdf', [
            'productos' => $productos,
            'simboloMoneda' => $simboloMoneda,
            'fecha' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('productos_' . date('Y-m-d_His') . '.pdf');
    }

    public function show(Producto $producto): RedirectResponse
    {
        return redirect()->route('mantenimiento.productos.edit', $producto);
    }

    public function create(): View
    {
        return view('pages.mantenimiento.productos.create', [
            'title' => 'Nuevo Producto',
            'lotes' => $this->getLotesOrdenados(),
            'categorias' => Categoria::orderBy('forma_farmaceutica')->get(),
            'presentaciones' => Presentacion::orderBy('presentacion')->get(),
            'laboratorios' => Cliente::where('tipo', 'laboratorio')->orderBy('nombres')->get(),
            'sintomas' => Sintoma::orderBy('sintoma')->get(),
            'tiposAfectacion' => TipoAfectacion::orderBy('idtipoa')->get(),
        ]);
    }

    public function store(StoreProductoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['idunidad'] = 1;
        if (empty($data['idcliente'])) {
            $lab = Cliente::where('tipo', 'laboratorio')->orderBy('idcliente')->first();
            $data['idcliente'] = $lab?->idcliente ?? 1;
        }
        $this->actualizarFechaVencimientoLote($data['idlote'], $request->input('fecha_vencimiento'));
        unset($data['fecha_vencimiento']);
        Producto::create($data);
        return $this->successRedirect('Producto registrado correctamente.', route('mantenimiento.productos.index'));
    }

    public function edit(Producto $producto): View
    {
        $producto->load(['lote', 'categoria', 'presentacion', 'laboratorio', 'sintoma', 'tipoAfectacion']);
        return view('pages.mantenimiento.productos.edit', [
            'title' => 'Editar Producto',
            'producto' => $producto,
            'lotes' => $this->getLotesOrdenados(),
            'categorias' => Categoria::orderBy('forma_farmaceutica')->get(),
            'presentaciones' => Presentacion::orderBy('presentacion')->get(),
            'laboratorios' => Cliente::where('tipo', 'laboratorio')->orderBy('nombres')->get(),
            'sintomas' => Sintoma::orderBy('sintoma')->get(),
            'tiposAfectacion' => TipoAfectacion::orderBy('idtipoa')->get(),
        ]);
    }

    public function update(UpdateProductoRequest $request, Producto $producto): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $this->actualizarFechaVencimientoLote($data['idlote'], $request->input('fecha_vencimiento'));
        unset($data['fecha_vencimiento']);
        $producto->update($data);
        return $this->successRedirect('Producto actualizado correctamente.', route('mantenimiento.productos.index'));
    }

    public function destroy(Request $request, Producto $producto): JsonResponse|RedirectResponse
    {
        $route = route('mantenimiento.productos.index');
        if (!$producto->puedeEliminar()) {
            return $this->errorRedirect($producto->mensajeNoEliminable(), $route);
        }
        DB::table('producto_similar')->where('idproducto', $producto->idproducto)->delete();
        $producto->delete();
        return $this->successRedirect('Producto eliminado correctamente.', $route);
    }

    /**
     * Búsqueda de productos para agregar como similar (JSON).
     */
    public function buscarParaSimilar(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $q = trim($q);
        if (strlen($q) < 2) {
            return response()->json([]);
        }
        $productos = Producto::query()
            ->join('presentacion', 'productos.idpresentacion', '=', 'presentacion.idpresentacion')
            ->where(function ($query) use ($q) {
                $term = '%' . $q . '%';
                $query->where('productos.descripcion', 'like', $term)
                    ->orWhere('productos.codigo', 'like', $term);
            })
            ->select('productos.idproducto', 'productos.descripcion', 'presentacion.presentacion as presentacion_nombre')
            ->limit(25)
            ->get();
        return response()->json($productos->map(fn ($p) => [
            'idproducto' => $p->idproducto,
            'descripcion' => $p->descripcion,
            'presentacion' => $p->presentacion_nombre ?? '',
        ]));
    }

    /**
     * Mostrar similares: por mismo síntoma/dolencia y los agregados manualmente.
     */
    public function similares(Producto $producto): View
    {
        $producto->load(['presentacion', 'sintoma']);
        $porSintoma = [];
        if ($producto->idsintoma) {
            $porSintoma = Producto::query()
                ->where('idsintoma', $producto->idsintoma)
                ->where('idproducto', '!=', $producto->idproducto)
                ->with('presentacion')
                ->orderBy('descripcion')
                ->get();
        }
        $agregados = DB::table('producto_similar')
            ->where('idproducto', $producto->idproducto)
            ->get();
        $simboloMoneda = Configuracion::first()?->simbolo_moneda ?? 'S/';

        return view('pages.mantenimiento.productos.similares', [
            'title' => 'Productos similares',
            'producto' => $producto,
            'porSintoma' => $porSintoma,
            'agregados' => $agregados,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }

    /**
     * Agregar un producto como similar (manual).
     */
    public function storeSimilar(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'idproducto_similar' => 'required|exists:productos,idproducto',
        ]);
        $otro = Producto::with('presentacion')->findOrFail($request->idproducto_similar);
        if ((int) $otro->idproducto === (int) $producto->idproducto) {
            return redirect()->route('mantenimiento.productos.similares', $producto)
                ->with('error', 'No puede agregar el mismo producto como similar.');
        }
        $existe = DB::table('producto_similar')
            ->where('idproducto', $producto->idproducto)
            ->where('producto', $otro->descripcion)
            ->where('presentacion', $otro->presentacion?->presentacion ?? '')
            ->exists();
        if ($existe) {
            return redirect()->route('mantenimiento.productos.similares', $producto)
                ->with('error', 'Ese producto ya está en la lista de similares.');
        }
        DB::table('producto_similar')->insert([
            'idproducto' => $producto->idproducto,
            'producto' => $otro->descripcion,
            'presentacion' => $otro->presentacion?->presentacion ?? '',
        ]);
        return redirect()->route('mantenimiento.productos.similares', $producto)
            ->with('success', 'Producto similar agregado.');
    }

    /**
     * Quitar un similar agregado manualmente (por producto + presentación).
     */
    public function destroySimilar(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'producto' => 'required|string|max:500',
            'presentacion' => 'required|string|max:255',
        ]);
        DB::table('producto_similar')
            ->where('idproducto', $producto->idproducto)
            ->where('producto', $request->producto)
            ->where('presentacion', $request->presentacion)
            ->delete();
        return redirect()->route('mantenimiento.productos.similares', $producto)
            ->with('success', 'Similar quitado.');
    }

    private function getLotesOrdenados()
    {
        return Lote::orderByRaw("CASE WHEN numero LIKE '%SIN LOTE%' OR numero = '0000' THEN 0 ELSE 1 END")
            ->orderBy('numero')
            ->get();
    }

    private function actualizarFechaVencimientoLote(int $idlote, ?string $fecha): void
    {
        if ($fecha && $idlote > 0) {
            Lote::where('idlote', $idlote)->update(['fecha_vencimiento' => $fecha]);
        }
    }
}
