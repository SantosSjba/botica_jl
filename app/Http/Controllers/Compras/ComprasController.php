<?php

namespace App\Http\Controllers\Compras;

use App\Http\Controllers\Controller;
use App\Models\Carritoc;
use App\Models\Compra;
use App\Models\Configuracion;
use App\Models\DetalleCompra;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ComprasController extends Controller
{
    public const TIPOS_DOC = ['FACTURA', 'BOLETA'];

    protected function sessionId(Request $request): string
    {
        $user = $request->user();
        return $user->usuario ?? (string) $user->getAuthIdentifier();
    }

    /**
     * Formulario para registrar una compra (carrito + datos de compra).
     */
    public function create(Request $request): View
    {
        $config = Configuracion::first();
        $simboloMoneda = $config->simbolo_moneda ?? 'S/';
        $impuesto = (float) ($config->impuesto ?? 18);
        $hoy = now()->toDateString();

        $items = Carritoc::where('session_id', $this->sessionId($request))->get();
        $subtotal = $items->sum('importe');
        $igv = round($subtotal * $impuesto / 100, 2);
        $total = round($subtotal + $igv, 2);

        return view('pages.compras.create', [
            'title' => 'Registrar compra',
            'items' => $items,
            'subtotal' => $subtotal,
            'igv' => $igv,
            'total' => $total,
            'impuesto' => $impuesto,
            'simboloMoneda' => $simboloMoneda,
            'fecha' => $hoy,
            'tiposDoc' => self::TIPOS_DOC,
        ]);
    }

    /**
     * Guardar la compra (desde el carrito), actualizar stock y vaciar carrito.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $sessionId = $this->sessionId($request);
        $items = Carritoc::where('session_id', $sessionId)->get();

        if ($items->isEmpty()) {
            return $this->errorRedirect(__('Debe agregar al menos un producto al carrito para registrar la compra.'), route('compras.create'));
        }

        $validated = $request->validate([
            'idcliente' => 'required|integer|exists:cliente,idcliente',
            'docu' => 'required|string|in:' . implode(',', self::TIPOS_DOC),
            'num_docu' => 'required|string|max:50',
            'fecha' => 'required|date',
        ], [], [
            'idcliente' => 'proveedor',
            'num_docu' => 'número de documento',
        ]);

        $config = Configuracion::first();
        $impuesto = (float) ($config->impuesto ?? 18);
        $subtotal = round($items->sum('importe'), 2);
        $igv = round($subtotal * $impuesto / 100, 2);
        $total = round($subtotal + $igv, 2);

        DB::beginTransaction();
        try {
            $compra = Compra::create([
                'idcliente' => $validated['idcliente'],
                'fecha' => $validated['fecha'],
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
                'docu' => $validated['docu'],
                'num_docu' => $validated['num_docu'],
            ]);

            foreach ($items as $item) {
                DetalleCompra::create([
                    'idcompra' => $compra->idcompra,
                    'idproducto' => $item->idproducto,
                    'cantidad' => $item->cantidad,
                    'precio' => $item->precio,
                    'importe' => $item->importe,
                ]);
                Producto::where('idproducto', $item->idproducto)
                    ->increment('stock', $item->cantidad);
            }

            Carritoc::where('session_id', $sessionId)->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('compras.create')
                ->with('error', __('Error al registrar la compra. Intente de nuevo.'));
        }

        return redirect()->route('compras.create')
            ->with('success', __('Compra registrada correctamente.'));
    }

    /**
     * Vaciar carrito y volver al formulario.
     */
    public function limpiar(Request $request): JsonResponse|RedirectResponse
    {
        Carritoc::where('session_id', $this->sessionId($request))->delete();
        return $this->successRedirect(__('Carrito vaciado.'), route('compras.create'));
    }

    /**
     * Buscar productos para autocompletado (precio_compra).
     */
    public function buscarProductos(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $q = trim($q);
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $productos = Producto::query()
            ->join('presentacion', 'productos.idpresentacion', '=', 'presentacion.idpresentacion')
            ->where('productos.estado', '1')
            ->where(function ($query) use ($q) {
                $term = '%' . $q . '%';
                $query->where('productos.descripcion', 'like', $term)
                    ->orWhere('productos.codigo', 'like', $term);
            })
            ->select('productos.idproducto', 'productos.codigo', 'productos.descripcion', 'productos.precio_compra', 'presentacion.presentacion')
            ->limit(20)
            ->get();

        return response()->json($productos->map(fn ($p) => [
            'idproducto' => $p->idproducto,
            'codigo' => $p->codigo,
            'descripcion' => $p->descripcion,
            'presentacion' => $p->presentacion,
            'precio' => number_format((float) $p->precio_compra, 2, '.', ''),
        ]));
    }

    /**
     * Buscar proveedores (clientes tipo laboratorio) para autocompletado.
     */
    public function buscarProveedores(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        $q = trim($q);
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $clientes = \App\Models\Cliente::query()
            ->where('tipo', 'laboratorio')
            ->where('nombres', 'like', '%' . $q . '%')
            ->select('idcliente', 'nombres')
            ->limit(20)
            ->get();

        return response()->json($clientes->map(fn ($c) => [
            'idcliente' => $c->idcliente,
            'nombres' => $c->nombres,
        ]));
    }

    /**
     * Agregar producto al carrito.
     */
    public function agregarItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idproducto' => 'required|integer|exists:productos,idproducto',
            'descripcion' => 'required|string|max:255',
            'presentacion' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
        ]);

        $sessionId = $this->sessionId($request);
        $exists = Carritoc::where('session_id', $sessionId)
            ->where('idproducto', $validated['idproducto'])
            ->exists();

        if ($exists) {
            return response()->json(['ok' => false, 'message' => 'El producto ya está en el carrito.']);
        }

        $precio = (float) $validated['precio'];
        $importe = round($precio * 1, 2);

        Carritoc::create([
            'idproducto' => $validated['idproducto'],
            'descripcion' => $validated['descripcion'],
            'presentacion' => $validated['presentacion'],
            'cantidad' => 1,
            'precio' => $precio,
            'importe' => $importe,
            'session_id' => $sessionId,
        ]);

        return $this->respuestaCarrito($request);
    }

    /**
     * Quitar producto del carrito.
     */
    public function quitarItem(Request $request): JsonResponse
    {
        $validated = $request->validate(['idproducto' => 'required|integer']);
        $sessionId = $this->sessionId($request);

        Carritoc::where('session_id', $sessionId)
            ->where('idproducto', $validated['idproducto'])
            ->delete();

        return $this->respuestaCarrito($request);
    }

    /**
     * Actualizar cantidad de un ítem.
     */
    public function actualizarCantidad(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idproducto' => 'required|integer',
            'cantidad' => 'required|numeric|min:0.01',
        ]);
        $sessionId = $this->sessionId($request);
        $cantidad = (int) round((float) $validated['cantidad'], 0);
        if ($cantidad < 1) {
            $cantidad = 1;
        }

        $item = Carritoc::where('session_id', $sessionId)
            ->where('idproducto', $validated['idproducto'])
            ->first();

        if (!$item) {
            return response()->json(['ok' => false, 'message' => 'Ítem no encontrado.']);
        }

        $importe = round((float) $item->precio * $cantidad, 2);
        Carritoc::where('session_id', $sessionId)
            ->where('idproducto', $validated['idproducto'])
            ->update(['cantidad' => $cantidad, 'importe' => $importe]);

        return $this->respuestaCarrito($request);
    }

    /**
     * Actualizar precio de un ítem.
     */
    public function actualizarPrecio(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'idproducto' => 'required|integer',
            'precio' => 'required|numeric|min:0',
        ]);
        $sessionId = $this->sessionId($request);
        $precio = (float) $validated['precio'];

        $item = Carritoc::where('session_id', $sessionId)
            ->where('idproducto', $validated['idproducto'])
            ->first();

        if (!$item) {
            return response()->json(['ok' => false, 'message' => 'Ítem no encontrado.']);
        }

        $importe = round($precio * (int) $item->cantidad, 2);
        Carritoc::where('session_id', $sessionId)
            ->where('idproducto', $validated['idproducto'])
            ->update(['precio' => $precio, 'importe' => $importe]);

        return $this->respuestaCarrito($request);
    }

    /**
     * Devolver HTML de tabla y totales (para AJAX).
     */
    public function partialsCarrito(Request $request): JsonResponse
    {
        return $this->respuestaCarrito($request);
    }

    /**
     * Respuesta JSON con HTML de tabla y totales para actualizar la vista.
     */
    protected function respuestaCarrito(Request $request): JsonResponse
    {
        $sessionId = $this->sessionId($request);
        $config = Configuracion::first();
        $impuesto = (float) ($config->impuesto ?? 18);
        $simboloMoneda = $config->simbolo_moneda ?? 'S/';

        $items = Carritoc::where('session_id', $sessionId)->get();
        $subtotal = $items->sum('importe');
        $igv = round($subtotal * $impuesto / 100, 2);
        $total = round($subtotal + $igv, 2);

        $tableHtml = view('pages.compras._carrito-tabla', ['items' => $items])->render();
        $totalesHtml = view('pages.compras._carrito-totales', [
            'subtotal' => $subtotal,
            'igv' => $igv,
            'total' => $total,
            'simboloMoneda' => $simboloMoneda,
        ])->render();

        return response()->json([
            'ok' => true,
            'table' => $tableHtml,
            'totales' => $totalesHtml,
        ]);
    }
}
