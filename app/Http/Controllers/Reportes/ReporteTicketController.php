<?php

namespace App\Http\Controllers\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Venta;
use App\Helpers\NumeroALetras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReporteTicketController extends Controller
{
    /**
     * Comprobante de venta para impresión (Ticket, A4 o A5).
     * GET /reportes/ticket?idventa=X&formato=ticket|a4|a5
     */
    public function index(Request $request)
    {
        $idventa = $request->query('idventa');
        $formato = $request->query('formato', 'ticket');
        if (!in_array($formato, ['ticket', 'a4', 'a5'], true)) {
            $formato = 'ticket';
        }

        if (empty($idventa)) {
            abort(404, 'Falta idventa');
        }

        $venta = Venta::with([
            'cliente.tipoDocumento',
            'serie',
            'detalles.producto.presentacion',
            'pagos',
        ])->find($idventa);

        if (!$venta) {
            abort(404, 'Venta no encontrada');
        }

        $config = Configuracion::first();
        $cantidadEnLetras = NumeroALetras::cantidadEnSoles((float) $venta->total);
        $logoUrl = $config && $config->logo && Storage::disk('public')->exists($config->logo)
            ? Storage::disk('public')->url($config->logo)
            : asset('images/logo/logo_oficial.png');

        return view('pages.reportes.comprobante-venta', [
            'venta' => $venta,
            'config' => $config,
            'formato' => $formato,
            'cantidadEnLetras' => $cantidadEnLetras,
            'logoUrl' => $logoUrl,
        ]);
    }
}
