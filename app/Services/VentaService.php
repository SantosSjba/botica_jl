<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\DetalleVenta;
use App\Models\PagoVenta;
use App\Models\Producto;
use App\Models\Serie;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VentaService
{
    /** @return string Session id para el carrito (usuario login). */
    public function getSessionId(): string
    {
        $user = auth()->user();
        return $user->usuario ?? (string) $user->getAuthIdentifier();
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, Carrito> */
    public function getCarritoItems()
    {
        return Carrito::where('session_id', $this->getSessionId())->orderBy('idproducto')->get();
    }

    /**
     * Totales del carrito: op_gravadas, op_exoneradas, op_inafectas, igv, total, simbolo_moneda.
     * @return array{op_gravadas: float, op_exoneradas: float, op_inafectas: float, igv: float, total: float, simbolo_moneda: string}
     */
    public function getTotales(): array
    {
        $sessionId = $this->getSessionId();
        $config = Configuracion::first();
        $impuesto = $config ? (float) $config->impuesto : 18;
        $simboloMoneda = $config->simbolo_moneda ?? 'S/';

        $opGravadas = (float) Carrito::where('session_id', $sessionId)->where('operacion', 'OP. GRAVADAS')->sum('valor_total');
        $opExoneradas = (float) Carrito::where('session_id', $sessionId)->where('operacion', 'OP. EXONERADAS')->sum('valor_total');
        $opInafectas = (float) Carrito::where('session_id', $sessionId)->where('operacion', 'OP. INAFECTAS')->sum('valor_total');
        $igv = (float) Carrito::where('session_id', $sessionId)->where('operacion', 'OP. GRAVADAS')->sum('igv');
        $total = (float) Carrito::where('session_id', $sessionId)->sum('importe_total');

        return [
            'op_gravadas' => round($opGravadas, 2),
            'op_exoneradas' => round($opExoneradas, 2),
            'op_inafectas' => round($opInafectas, 2),
            'igv' => round($igv, 2),
            'total' => round($total, 2),
            'simbolo_moneda' => $simboloMoneda,
        ];
    }

    /** Agregar al carrito por código de barras. Retorna mensaje de éxito o error. */
    public function addByBarcode(string $codigo): string
    {
        $producto = Producto::query()
            ->join('tipo_afectacion', 'productos.idtipoaf', '=', 'tipo_afectacion.idtipoa')
            ->join('presentacion', 'productos.idpresentacion', '=', 'presentacion.idpresentacion')
            ->where('productos.codigo', $codigo)
            ->where('productos.estado', '1')
            ->select('productos.idproducto', 'productos.descripcion', 'productos.precio_venta', 'productos.stock', 'tipo_afectacion.descripcion as operacion', 'presentacion.presentacion as presentacion_nombre')
            ->first();

        if (!$producto) {
            return 'Producto no encontrado.';
        }
        if ((int) $producto->stock <= 0) {
            return 'No hay stock disponible (stock 0). No se puede agregar.';
        }

        $sessionId = $this->getSessionId();
        if (Carrito::where('session_id', $sessionId)->where('idproducto', $producto->idproducto)->exists()) {
            return 'El producto ya fue agregado al carrito.';
        }

        return $this->addProductToCart(
            (int) $producto->idproducto,
            $producto->descripcion,
            $producto->presentacion_nombre ?? '',
            (float) $producto->precio_venta,
            $producto->operacion ?? 'OP. GRAVADAS'
        );
    }

    /** Agregar al carrito por id de producto (desde modal). $cantidad respeta stock. */
    public function addByProduct(int $idproducto, string $descripcion, string $presentacion, float $precioVenta, int $cantidad = 1): string
    {
        $producto = Producto::with('tipoAfectacion')->find($idproducto);
        if (!$producto || $producto->estado != '1') {
            return 'Producto no válido.';
        }
        if ($producto->stock <= 0) {
            return 'No hay stock disponible (stock 0). No se puede agregar.';
        }
        $cantidad = max(1, min((int) $cantidad, (int) $producto->stock));

        $sessionId = $this->getSessionId();
        if (Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->exists()) {
            return 'El producto ya fue agregado al carrito.';
        }

        $operacion = $producto->tipoAfectacion?->descripcion ?? 'OP. GRAVADAS';
        return $this->addProductToCart($idproducto, $descripcion, $presentacion, $precioVenta, $operacion, $cantidad);
    }

    private function addProductToCart(int $idproducto, string $descripcion, string $presentacion, float $precioVenta, string $operacion, int $cantidad = 1): string
    {
        $config = Configuracion::first();
        $impuesto = $config ? (float) $config->impuesto : 18;
        $cant = max(1, $cantidad);
        $sessionId = $this->getSessionId();

        if ($operacion === 'OP. GRAVADAS') {
            $pU = round($precioVenta, 2);
            $puG = round($precioVenta / (1 + ($impuesto / 100)), 6);
            $igvG = round(($impuesto / 100) * $puG * $cant, 2);
            $vT = round($puG * $cant, 6);
            $impT = round($pU * $cant, 2);
            Carrito::create([
                'idproducto' => $idproducto,
                'descripcion' => $descripcion,
                'presentacion' => $presentacion,
                'cantidad' => $cant,
                'valor_unitario' => $puG,
                'precio_unitario' => $pU,
                'igv' => $igvG,
                'porcentaje_igv' => $impuesto,
                'valor_total' => $vT,
                'importe_total' => $impT,
                'operacion' => $operacion,
                'session_id' => $sessionId,
            ]);
        } else {
            $pU = round($precioVenta, 2);
            $igv = 0.0;
            $vT = round($precioVenta * $cant, 2);
            $imp = $vT;
            Carrito::create([
                'idproducto' => $idproducto,
                'descripcion' => $descripcion,
                'presentacion' => $presentacion,
                'cantidad' => $cant,
                'valor_unitario' => $pU,
                'precio_unitario' => $pU,
                'igv' => $igv,
                'porcentaje_igv' => $impuesto,
                'valor_total' => $vT,
                'importe_total' => $imp,
                'operacion' => $operacion,
                'session_id' => $sessionId,
            ]);
        }
        Producto::where('idproducto', $idproducto)->decrement('stock', $cant);
        return 'Producto agregado al carrito';
    }

    /** Actualizar cantidad en carrito (solo enteros). Ajusta stock: devuelve si baja, reserva si sube. */
    public function updateCantidad(int $idproducto, int $cantidad): string
    {
        $cantidad = max(1, $cantidad);
        $sessionId = $this->getSessionId();
        $item = Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->first();
        if (!$item) {
            return 'Item no encontrado.';
        }
        $cantidadAnterior = (int) $item->cantidad;
        if ($cantidad === $cantidadAnterior) {
            return 'Actualizado';
        }
        if ($cantidad > $cantidadAnterior) {
            $diferencia = $cantidad - $cantidadAnterior;
            $stock = (int) Producto::where('idproducto', $idproducto)->value('stock');
            if ($diferencia > $stock) {
                return 'No cuenta con el stock suficiente.';
            }
            Producto::where('idproducto', $idproducto)->decrement('stock', $diferencia);
        } else {
            Producto::where('idproducto', $idproducto)->increment('stock', $cantidadAnterior - $cantidad);
        }
        $config = Configuracion::first();
        $impuesto = $config ? (float) $config->impuesto : 18;
        $puC = (float) $item->precio_unitario;
        $ope = $item->operacion;

        if ($ope === 'OP. GRAVADAS') {
            $puG = round($puC / (1 + ($impuesto / 100)), 6);
            $vT = round($puG * $cantidad, 6);
            $imp = round($cantidad * $puC, 2);
            $igv = round(($impuesto / 100) * $puG * $cantidad, 2);
            Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->update([
                'cantidad' => $cantidad, 'valor_total' => $vT, 'importe_total' => $imp, 'igv' => $igv,
            ]);
        } else {
            $imp = round($puC * $cantidad, 2);
            Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->update([
                'cantidad' => $cantidad, 'valor_total' => $imp, 'importe_total' => $imp,
            ]);
        }
        return 'Actualizado';
    }

    /** Actualizar precio unitario en carrito. */
    public function updatePrecio(int $idproducto, float $precioUnitario): string
    {
        $sessionId = $this->getSessionId();
        $item = Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->first();
        if (!$item) {
            return 'Item no encontrado.';
        }
        $config = Configuracion::first();
        $impuesto = $config ? (float) $config->impuesto : 18;
        $cant = (float) $item->cantidad;
        $ope = $item->operacion;

        if ($ope === 'OP. GRAVADAS') {
            $text = round($precioUnitario, 2);
            $vuG = round($text / (1 + ($impuesto / 100)), 6);
            $igv = round(($impuesto / 100) * $vuG * $cant, 2);
            $vT = round($vuG * $cant, 6);
            $iT = round($text * $cant, 2);
            Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->update([
                'precio_unitario' => $text, 'valor_unitario' => $vuG, 'igv' => $igv, 'valor_total' => $vT, 'importe_total' => $iT,
            ]);
        } else {
            $imp = round($cant * $precioUnitario, 2);
            Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->update([
                'precio_unitario' => $precioUnitario, 'valor_unitario' => $precioUnitario, 'valor_total' => $imp, 'importe_total' => $imp,
            ]);
        }
        return 'Actualizado';
    }

    /** Quitar ítem del carrito y devolver su cantidad al stock del producto. */
    public function removeItem(int $idproducto): string
    {
        $sessionId = $this->getSessionId();
        $item = Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->first();
        if ($item) {
            $cantidad = (int) $item->cantidad;
            Carrito::where('session_id', $sessionId)->where('idproducto', $idproducto)->delete();
            Producto::where('idproducto', $idproducto)->increment('stock', $cantidad);
        }
        return 'Producto quitado del carrito';
    }

    /** Vaciar carrito del usuario y devolver todo el stock reservado. */
    public function limpiar(): void
    {
        $sessionId = $this->getSessionId();
        $items = Carrito::where('session_id', $sessionId)->get();
        foreach ($items as $row) {
            Producto::where('idproducto', $row->idproducto)->increment('stock', (int) $row->cantidad);
        }
        Carrito::where('session_id', $sessionId)->delete();
    }

    /** Siguiente correlativo para tipo de comprobante (00=TICKET, 01=FACTURA, 03=BOLETA). */
    public function getSiguienteCorrelativo(string $tipocomp): int
    {
        $max = Serie::where('tipocomp', $tipocomp)->max('correlativo');
        return $max ? (int) $max + 1 : 11;
    }

    /**
     * Guardar venta: venta + detalle + actualizar stock + vaciar carrito.
     * @param array{tipo_comp: string, serie: string, correlativo: int, fecha_emision: string, forma_pago: string, numope?: string, tipo_doc: int, numero_doc: string, razon_social: string, direccion: string, efectivo?: float, vuelto?: float} $data
     * @return array{success: bool, message: string, idventa?: int}
     */
    public function guardarVenta(array $data): array
    {
        $sessionId = $this->getSessionId();
        $items = Carrito::where('session_id', $sessionId)->get();
        if ($items->isEmpty()) {
            return ['success' => false, 'message' => 'No se pudo registrar la venta. Agregue productos al carrito.'];
        }

        $correlativo = (int) ($data['correlativo'] ?? 0);
        $tipocomp = $data['tipo_comp'] ?? '00';
        $correlativoExistente = (int) Serie::where('tipocomp', $tipocomp)->max('correlativo');
        if ($correlativoExistente >= $correlativo && $correlativo > 0) {
            return ['success' => false, 'message' => 'El comprobante ya se encuentra registrado, favor volver a intentarlo.'];
        }

        $config = Configuracion::first();
        $idconf = 1;
        if ($config && isset($config->idconf)) {
            $idconf = (int) $config->idconf;
        } elseif ($config) {
            $idconf = (int) $config->getKey();
        }

        $totales = $this->getTotales();
        $user = auth()->user();
        $idusuario = $user->idusu ?? $user->getAuthIdentifier();

        $numeroDoc = trim($data['numero_doc'] ?? '');
        if ($numeroDoc === '') {
            $numeroDoc = '00000000';
        }
        $td = (int) ($data['tipo_doc'] ?? 2);
        if ($td === 1) {
            $td = 2;
        }
        $rz = trim($data['razon_social'] ?? 'CLIENTE VARIOS');
        $direccion = trim($data['direccion'] ?? '');

        $cliente = Cliente::where('nrodoc', $numeroDoc)->first();
        if (!$cliente) {
            $cliente = Cliente::create([
                'nombres' => $rz,
                'direccion' => $direccion,
                'id_tipo_docu' => $td,
                'nrodoc' => $numeroDoc,
                'tipo' => 'cliente',
            ]);
        }
        $idcliente = $cliente->idcliente;

        $idserie = (int) Serie::max('idserie') + 1;
        if ($idserie < 1) {
            $idserie = 1;
        }
        Serie::create([
            'idserie' => $idserie,
            'tipocomp' => $tipocomp,
            'serie' => $data['serie'] ?? 'T001',
            'correlativo' => $correlativo,
        ]);

        $fechaSolo = $data['fecha_emision'] ?? Carbon::today()->format('Y-m-d');
        $fechaEmision = Carbon::parse($fechaSolo)->format('Y-m-d') . ' ' . Carbon::now()->format('H:i:s');
        $totalVenta = (float) $totales['total'];
        $pagos = $data['pagos'] ?? null;

        if (!empty($pagos) && is_array($pagos)) {
            $sumaPagos = 0;
            foreach ($pagos as $p) {
                $sumaPagos += (float) ($p['monto'] ?? 0);
            }
            if (abs($sumaPagos - $totalVenta) > 0.01) {
                return ['success' => false, 'message' => 'La suma de los pagos ('.number_format($sumaPagos, 2).') no coincide con el total a pagar ('.number_format($totalVenta, 2).').'];
            }
            $tipos = array_unique(array_column($pagos, 'tipo_pago'));
            $formaPago = implode(', ', $tipos);
            $efectivo = null;
            $vuelto = null;
            $numope = '';
            foreach ($pagos as $p) {
                if (($p['tipo_pago'] ?? '') === 'EFECTIVO') {
                    $recibo = (float) ($p['recibo'] ?? $p['monto'] ?? 0);
                    $montoEfe = (float) ($p['monto'] ?? 0);
                    $efectivo = $recibo > 0 ? $recibo : null;
                    $vuelto = $efectivo !== null ? max(0, $efectivo - $montoEfe) : null;
                    break;
                }
            }
            foreach ($pagos as $p) {
                $n = trim($p['numope'] ?? '');
                if ($n !== '') { $numope = $n; break; }
            }
        } else {
            $formaPago = $data['forma_pago'] ?? 'EFECTIVO';
            $numope = $data['numope'] ?? '';
            $efectivo = isset($data['efectivo']) ? (float) $data['efectivo'] : null;
            $vuelto = isset($data['vuelto']) ? (float) $data['vuelto'] : null;
        }

        try {
            DB::beginTransaction();

            $idventa = (int) Venta::max('idventa') + 1;
            if ($idventa < 1) {
                $idventa = 1;
            }

            Venta::create([
                'idventa' => $idventa,
                'idconf' => $idconf,
                'tipocomp' => $tipocomp,
                'idcliente' => $idcliente,
                'idusuario' => $idusuario,
                'idserie' => $idserie,
                'fecha_emision' => $fechaEmision,
                'op_gravadas' => $totales['op_gravadas'],
                'op_exoneradas' => $totales['op_exoneradas'],
                'op_inafectas' => $totales['op_inafectas'],
                'igv' => $totales['igv'],
                'total' => $totales['total'],
                'estado' => 'no_enviado',
                'numope' => $numope,
                'formadepago' => $formaPago,
                'efectivo' => $efectivo,
                'vuelto' => $vuelto,
                'tire' => $data['tire'] ?? 'noenviado',
            ]);

            if (!empty($pagos) && is_array($pagos)) {
                foreach ($pagos as $p) {
                    $tipo = trim($p['tipo_pago'] ?? 'EFECTIVO');
                    $monto = (float) ($p['monto'] ?? 0);
                    if ($monto <= 0) continue;
                    $recibo = isset($p['recibo']) ? (float) $p['recibo'] : null;
                    $numopePago = trim($p['numope'] ?? '');
                    PagoVenta::create([
                        'idventa' => $idventa,
                        'tipo_pago' => $tipo,
                        'monto' => $monto,
                        'recibo' => $tipo === 'EFECTIVO' ? $recibo : null,
                        'numope' => $numopePago !== '' ? $numopePago : null,
                    ]);
                }
            } else {
                PagoVenta::create([
                    'idventa' => $idventa,
                    'tipo_pago' => $formaPago,
                    'monto' => $totalVenta,
                    'recibo' => $efectivo,
                    'numope' => $numope !== '' ? $numope : null,
                ]);
            }

            $item = 0;
            foreach ($items as $row) {
                $item++;
                DetalleVenta::create([
                    'idventa' => $idventa,
                    'item' => $item,
                    'idproducto' => $row->idproducto,
                    'cantidad' => $row->cantidad,
                    'valor_unitario' => $row->valor_unitario,
                    'precio_unitario' => $row->precio_unitario,
                    'igv' => $row->igv,
                    'porcentaje_igv' => $config ? (float) $config->impuesto : 18,
                    'valor_total' => $row->valor_total,
                    'importe_total' => $row->importe_total,
                    'descuento' => isset($row->descuento) ? (float) $row->descuento : 0,
                ]);
                // Stock ya fue reservado al agregar al carrito; no volver a decrementar
            }

            Carrito::where('session_id', $sessionId)->delete();
            DB::commit();

            return ['success' => true, 'message' => 'Venta registrada.', 'idventa' => $idventa];
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return ['success' => false, 'message' => 'Error al procesar la venta: ' . $e->getMessage()];
        }
    }
}
