<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Services\VentaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function __construct(
        protected VentaService $ventaService
    ) {}

    /** Página principal del formulario de venta. */
    public function index(): View
    {
        $config = \App\Models\Configuracion::first();
        $simboloMoneda = $config?->simbolo_moneda ?? 'S/';
        $items = $this->ventaService->getCarritoItems();
        $totales = $this->ventaService->getTotales();
        return view('pages.ventas.index', [
            'title' => 'Venta',
            'simboloMoneda' => $simboloMoneda,
            'items' => $items,
            'totales' => $totales,
        ]);
    }

    /** Partial: tabla del carrito (para refresco AJAX). */
    public function carrito(): View
    {
        $items = $this->ventaService->getCarritoItems();
        $simboloMoneda = \App\Models\Configuracion::first()?->simbolo_moneda ?? 'S/';
        return view('pages.ventas._carrito', [
            'items' => $items,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }

    /** Partial: total a pagar (para refresco AJAX). */
    public function total(): View
    {
        $totales = $this->ventaService->getTotales();
        return view('pages.ventas._total', ['totales' => $totales]);
    }

    /** Partial: resumen IGV (para refresco AJAX). */
    public function igv(): View
    {
        $totales = $this->ventaService->getTotales();
        return view('pages.ventas._igv', ['totales' => $totales]);
    }

    /** Agregar al carrito por código de barras. */
    public function addBarcode(Request $request): JsonResponse
    {
        $cod = $request->input('cod', '');
        $cod = is_string($cod) ? trim($cod) : '';
        if ($cod === '') {
            return response()->json(['success' => false, 'message' => 'Ingrese el código.']);
        }
        $message = $this->ventaService->addByBarcode($cod);
        $success = str_contains($message, 'agregado') || str_contains($message, 'Agregado');
        return response()->json(['success' => $success, 'message' => $message]);
    }

    /** Agregar al carrito por producto (desde modal). */
    public function addProduct(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'idproducto' => 'required|integer',
            'des' => 'required|string|max:500',
            'pres' => 'nullable|string|max:100',
            'pre' => 'required|numeric|min:0',
            'cantidad' => 'nullable|integer|min:1',
        ])->validate();
        $cantidad = isset($validated['cantidad']) ? (int) $validated['cantidad'] : 1;
        $message = $this->ventaService->addByProduct(
            (int) $validated['idproducto'],
            $validated['des'],
            $validated['pres'] ?? '',
            (float) $validated['pre'],
            $cantidad
        );
        $success = str_contains($message, 'agregado') || str_contains($message, 'Agregado');
        return response()->json(['success' => $success, 'message' => $message]);
    }

    /** Actualizar cantidad en carrito (solo enteros). */
    public function updateCantidad(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');
        $cantidad = (int) round((float) str_replace(',', '.', $request->input('text', '0')));
        if ($id < 1) {
            return response()->json(['success' => false, 'message' => 'ID inválido.']);
        }
        if ($cantidad < 1) {
            return response()->json(['success' => false, 'message' => 'La cantidad debe ser al menos 1.']);
        }
        $message = $this->ventaService->updateCantidad($id, $cantidad);
        return response()->json(['success' => $message === 'Actualizado', 'message' => $message]);
    }

    /** Actualizar precio unitario en carrito. */
    public function updatePrecio(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');
        $text = (float) str_replace(',', '.', $request->input('text', '0'));
        if ($id < 1) {
            return response()->json(['success' => false, 'message' => 'ID inválido.']);
        }
        $message = $this->ventaService->updatePrecio($id, $text);
        return response()->json(['success' => $message === 'Actualizado', 'message' => $message]);
    }

    /** Quitar ítem del carrito. */
    public function removeItem(Request $request): JsonResponse
    {
        $id = (int) $request->input('id');
        if ($id < 1) {
            return response()->json(['success' => false, 'message' => 'ID inválido.']);
        }
        $this->ventaService->removeItem($id);
        return response()->json(['success' => true, 'message' => 'Producto quitado del carrito']);
    }

    /** Obtener siguiente correlativo para tipo de comprobante. */
    public function correlativo(Request $request): JsonResponse
    {
        $tico = $request->input('tico', '00');
        $numero = $this->ventaService->getSiguienteCorrelativo($tico);
        return response()->json(['correlativo' => $numero]);
    }

    /** Guardar venta. Acepta forma única (forma, recibo, vuelto) o múltiples pagos (pagos[]). */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tico' => 'required|string|in:00,01,03',
            'serie' => 'required|string|max:20',
            'correl' => 'required|integer|min:1',
            'fecha' => 'required|date',
            'forma' => 'nullable|string|max:50',
            'numope' => 'nullable|string|max:50',
            'td' => 'required|integer|min:1|max:6',
            'numero' => 'nullable|string|max:20',
            'rz' => 'required|string|max:500',
            'dir' => 'nullable|string|max:500',
            'recibo' => 'nullable|numeric|min:0',
            'vuelto' => 'nullable|numeric|min:0',
            'pagos' => 'nullable|array',
            'pagos.*.tipo_pago' => 'required_with:pagos|string|in:EFECTIVO,YAPE,PLIN,TRANSFERENCIA,TARJETA,DEPOSITO EN CUENTA,OTRO',
            'pagos.*.monto' => 'required_with:pagos|numeric|min:0.01',
            'pagos.*.recibo' => 'nullable|numeric|min:0',
            'pagos.*.numope' => 'nullable|string|max:100',
        ], [], [
            'tico' => 'tipo comprobante',
            'serie' => 'serie',
            'correl' => 'correlativo',
            'fecha' => 'fecha emisión',
            'forma' => 'forma de pago',
            'td' => 'tipo documento',
            'numero' => 'número documento',
            'rz' => 'cliente / razón social',
        ]);

        $totales = $this->ventaService->getTotales();
        $totalVenta = $totales['total'];
        $pagos = $request->input('pagos');
        $usarPagos = is_array($pagos) && count(array_filter($pagos, fn ($p) => (float) ($p['monto'] ?? 0) > 0)) > 0;

        if ($usarPagos) {
            $validated['forma'] = $validated['forma'] ?? 'EFECTIVO';
        } else {
            if (empty($validated['forma'])) {
                return response()->json(['success' => false, 'message' => 'Seleccione forma de pago o agregue al menos un pago.'], 422);
            }
        }

        $data = [
            'tipo_comp' => $validated['tico'],
            'serie' => $validated['serie'],
            'correlativo' => (int) $validated['correl'],
            'fecha_emision' => $validated['fecha'],
            'forma_pago' => $validated['forma'],
            'numope' => $validated['numope'] ?? '',
            'tipo_doc' => (int) $validated['td'],
            'numero_doc' => trim($validated['numero'] ?? ''),
            'razon_social' => trim($validated['rz']),
            'direccion' => trim($validated['dir'] ?? ''),
        ];
        if ($usarPagos) {
            $data['pagos'] = $pagos;
        } else {
            if ($validated['forma'] === 'EFECTIVO' && isset($validated['recibo'])) {
                $data['efectivo'] = (float) $validated['recibo'];
                $data['vuelto'] = (float) ($validated['vuelto'] ?? max(0, (float) $validated['recibo'] - $totalVenta));
            }
        }

        $result = $this->ventaService->guardarVenta($data);
        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }
        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'idventa' => $result['idventa'] ?? null,
        ]);
    }

    /** Limpiar carrito y redirigir al formulario. */
    public function limpiar(): \Illuminate\Http\RedirectResponse
    {
        $this->ventaService->limpiar();
        return redirect()->route('ventas.index')->with('success', 'Carrito vaciado.');
    }

    /** Listado de productos para el modal de búsqueda (partial). */
    public function productos(Request $request): View
    {
        $buscar = $request->input('buscar', '');
        $query = DB::table('productos')
            ->join('presentacion', 'productos.idpresentacion', '=', 'presentacion.idpresentacion')
            ->join('sintoma', 'productos.idsintoma', '=', 'sintoma.idsintoma')
            ->join('tipo_afectacion', 'productos.idtipoaf', '=', 'tipo_afectacion.idtipoa')
            ->where('productos.estado', '1')
            ->select(
                'productos.idproducto',
                'productos.codigo',
                'productos.descripcion',
                'productos.stock',
                'productos.precio_venta',
                'presentacion.presentacion as presentacion_nombre',
                'sintoma.sintoma as sintoma_nombre',
                'tipo_afectacion.descripcion as operacion'
            );
        if ($buscar !== '') {
            $term = '%' . $buscar . '%';
            $query->where(function ($q) use ($term) {
                $q->where('productos.codigo', 'like', $term)
                    ->orWhere('productos.descripcion', 'like', $term)
                    ->orWhere('presentacion.presentacion', 'like', $term)
                    ->orWhere('sintoma.sintoma', 'like', $term);
            });
        }
        $productos = $query->orderBy('productos.descripcion')->paginate(10)->withQueryString();
        $simboloMoneda = \App\Models\Configuracion::first()?->simbolo_moneda ?? 'S/';
        return view('pages.ventas._modal-productos', [
            'productos' => $productos,
            'buscar' => $buscar,
            'simboloMoneda' => $simboloMoneda,
        ]);
    }
}
