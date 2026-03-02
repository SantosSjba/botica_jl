@php
    $tipocompDesc = match($venta->tipocomp ?? '') {
        '01' => 'FACTURA',
        '03' => 'BOLETA',
        default => 'TICKET',
    };
    $cliente = $venta->cliente;
    $serie = $venta->serie;
    $tipoDocCliente = $cliente && $cliente->tipoDocumento ? $cliente->tipoDocumento->descripcion : 'DOC';
    $baseUrl = url('/reportes/ticket?idventa=' . $venta->idventa);
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $tipocompDesc }} {{ $serie->serie ?? '' }}-{{ $serie->correlativo ?? '' }}</title>
    <style>
        :root {
            --toolbar-bg: #1e293b;
            --toolbar-text: #f1f5f9;
            --toolbar-border: #334155;
            --btn-format: #334155;
            --btn-format-hover: #475569;
            --btn-active-bg: #3b82f6;
            --btn-active-text: #fff;
            --btn-print-bg: #22c55e;
            --btn-print-hover: #16a34a;
        }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, Arial, sans-serif; background: #f1f5f9; min-height: 100vh; }
        .no-print {
            position: sticky; top: 0; z-index: 100;
            background: var(--toolbar-bg);
            color: var(--toolbar-text);
            padding: 14px 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 16px;
        }
        .no-print .toolbar-label { font-size: 13px; font-weight: 600; color: #94a3b8; margin-right: 4px; }
        .no-print .format-group {
            display: inline-flex;
            background: rgba(0,0,0,0.2);
            border-radius: 10px;
            padding: 4px;
            gap: 2px;
        }
        .no-print .format-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            color: #cbd5e1;
            background: transparent;
            transition: background 0.2s, color 0.2s;
        }
        .no-print .format-btn:hover { background: var(--btn-format-hover); color: #fff; }
        .no-print .format-btn.active { background: var(--btn-active-bg); color: var(--btn-active-text); }
        .no-print .format-btn .size { font-size: 11px; opacity: 0.85; }
        .no-print .print-btn {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            background: var(--btn-print-bg);
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 2px 6px rgba(34, 197, 94, 0.35);
        }
        .no-print .print-btn:hover { background: var(--btn-print-hover); }
        .no-print .print-btn svg { width: 20px; height: 20px; flex-shrink: 0; }
        .comprobante-wrap { padding: 0 20px 24px; }
        .comprobante {
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            padding: 16px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .comprobante table { width: 100%; border-collapse: collapse; }
        .comprobante th, .comprobante td { padding: 4px 6px; text-align: left; vertical-align: top; }
        .comprobante .text-right { text-align: right; }
        .comprobante .text-center { text-align: center; }
        .comprobante hr { border: none; border-top: 1px dashed #333; margin: 6px 0; }
        .comprobante .total-row { font-weight: bold; }

        /* Ticket: ancho 80mm en pantalla ~302px */
        .formato-ticket .comprobante-wrap { max-width: 340px; margin: 0 auto; padding: 0 12px 24px; }
        .formato-ticket .comprobante { max-width: 302px; font-size: 11px; padding: 10px; }
        .formato-ticket .comprobante img { max-width: 100px; max-height: 80px; }
        .formato-ticket .comprobante th, .formato-ticket .comprobante td { padding: 2px 4px; font-size: 11px; }

        /* A4 */
        .formato-a4 .comprobante-wrap { max-width: 230mm; margin: 0 auto; }
        .formato-a4 .comprobante { max-width: 210mm; font-size: 12px; }
        .formato-a4 .comprobante img { max-height: 80px; }

        /* A5 */
        .formato-a5 .comprobante-wrap { max-width: 168mm; margin: 0 auto; }
        .formato-a5 .comprobante { max-width: 148mm; font-size: 11px; }
        .formato-a5 .comprobante img { max-height: 70px; }

        @media print {
            body { background: #fff; padding: 0 !important; }
            .no-print { display: none !important; }
            .comprobante-wrap { padding: 0 !important; max-width: none !important; }
            .comprobante { border: none !important; box-shadow: none !important; border-radius: 0 !important; margin: 0 !important; }
            .formato-ticket .comprobante { width: 80mm !important; max-width: 80mm !important; }
            .formato-a4 .comprobante { width: 100% !important; max-width: 100% !important; }
            .formato-a5 .comprobante { width: 100% !important; max-width: 100% !important; }
        }
        @if($formato === 'ticket')
        @page { size: 80mm auto; margin: 3mm; }
        @elseif($formato === 'a4')
        @page { size: A4; margin: 12mm; }
        @else
        @page { size: A5; margin: 10mm; }
        @endif
    </style>
</head>
<body class="formato-{{ $formato }}">
    <div class="no-print">
        <span class="toolbar-label">Formato de impresión</span>
        <div class="format-group">
            <button type="button" class="format-btn {{ $formato === 'ticket' ? 'active' : '' }}" onclick="cambiarFormato('ticket')" title="Ticket térmico 80 mm">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9h.01M6 15h.01M10 9h.01M10 15h.01M14 9h.01M14 15h.01M18 9h.01M18 15h.01"/><rect x="3" y="4" width="18" height="16" rx="2"/></svg>
                Ticket <span class="size">80 mm</span>
            </button>
            <button type="button" class="format-btn {{ $formato === 'a4' ? 'active' : '' }}" onclick="cambiarFormato('a4')" title="Hoja A4">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M3 9h18M9 21V9"/></svg>
                A4
            </button>
            <button type="button" class="format-btn {{ $formato === 'a5' ? 'active' : '' }}" onclick="cambiarFormato('a5')" title="Hoja A5">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M3 9h18M9 21V9"/></svg>
                A5
            </button>
        </div>
        <button type="button" class="print-btn" onclick="window.print()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
            Imprimir
        </button>
    </div>
    <div class="comprobante-wrap">
    <div class="comprobante">
        <table>
            <tr>
                <td colspan="2" class="text-center">
                    <img src="{{ $logoUrl }}" alt="Logo" style="object-fit: contain;">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">{{ $config->razon_social ?? $config->nombre_comercial ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-center">RUC: {{ $config->ruc ?? '' }}</td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
            <tr>
                <td><strong>{{ $tipocompDesc }} ELECTRÓNICA:</strong></td>
                <td class="text-right"><strong>{{ $serie->serie ?? '' }} - {{ $serie->correlativo ?? '' }}</strong></td>
            </tr>
            <tr>
                <td>CLIENTE</td>
                <td>{{ $cliente->nombres ?? 'PÚBLICO' }}</td>
            </tr>
            <tr>
                <td>DIRECCIÓN</td>
                <td>{{ $cliente->direccion ?? '-' }}</td>
            </tr>
            <tr>
                <td>{{ $tipoDocCliente }}</td>
                <td>{{ $cliente->nrodoc ?? '-' }}</td>
            </tr>
            <tr>
                <td>FECHA DE EMISIÓN:</td>
                <td>{{ $venta->fecha_emision ? $venta->fecha_emision->format('d/m/Y H:i') : '' }}</td>
            </tr>
            <tr>
                <td>TIPO DE TRANSACCIÓN</td>
                <td>CONTADO</td>
            </tr>
            <tr><td colspan="2"><hr></td></tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th>CANT.</th>
                    <th>PRODUCTO</th>
                    <th class="text-right">P.U.</th>
                    <th class="text-right">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $d)
                    @php
                        $prod = $d->producto;
                        $presentacion = $prod && $prod->presentacion ? $prod->presentacion->presentacion : '';
                        $descProd = $prod ? $prod->descripcion : '-';
                        if ($presentacion) $descProd .= ' / ' . $presentacion;
                        $pu = $d->precio_unitario ?? $d->valor_unitario ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $d->item }}</td>
                        <td>{{ number_format($d->cantidad, 2, '.', '') }}</td>
                        <td>{{ $descProd }}</td>
                        <td class="text-right">{{ number_format($pu, 2, '.', '') }}</td>
                        <td class="text-right">{{ number_format($d->importe_total ?? $d->valor_total ?? 0, 2, '.', '') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <table>
            <tr>
                <td colspan="3"></td>
                <td class="text-right">OP. GRAVADAS:</td>
                <td class="text-right">{{ number_format($venta->op_gravadas ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td class="text-right">IGV (18%):</td>
                <td class="text-right">{{ number_format($venta->igv ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td class="text-right">OP. EXONERADAS:</td>
                <td class="text-right">{{ number_format($venta->op_exoneradas ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td colspan="3"></td>
                <td class="text-right">OP. INAFECTAS:</td>
                <td class="text-right">{{ number_format($venta->op_inafectas ?? 0, 2, '.', '') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="3"></td>
                <td class="text-right">IMPORTE TOTAL:</td>
                <td class="text-right">{{ number_format($venta->total ?? 0, 2, '.', '') }}</td>
            </tr>
        </table>
        <hr>
        <table>
            @if($venta->pagos && $venta->pagos->isNotEmpty())
                <tr>
                    <td colspan="3"></td>
                    <td class="text-right"><strong>FORMA DE PAGO</strong></td>
                    <td></td>
                </tr>
                @foreach($venta->pagos as $pago)
                    <tr>
                        <td colspan="3"></td>
                        <td class="text-right">{{ $pago->tipo_pago }}:</td>
                        <td class="text-right">{{ number_format($pago->monto, 2, '.', '') }}</td>
                    </tr>
                    @if($pago->tipo_pago === 'EFECTIVO' && (float)($pago->recibo ?? 0) > 0)
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right">Recibido:</td>
                            <td class="text-right">{{ number_format($pago->recibo, 2, '.', '') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right">Vuelto:</td>
                            <td class="text-right">{{ number_format(max(0, (float)$pago->recibo - (float)$pago->monto), 2, '.', '') }}</td>
                        </tr>
                    @endif
                    @if(!empty($pago->numope))
                        <tr>
                            <td colspan="3"></td>
                            <td class="text-right">Nº operación:</td>
                            <td class="text-right">{{ $pago->numope }}</td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr>
                    <td colspan="3"></td>
                    <td class="text-right">FORMA DE PAGO:</td>
                    <td class="text-right">{{ $venta->formadepago ?? 'EFECTIVO' }}</td>
                </tr>
                @if(!empty($venta->numope))
                <tr>
                    <td colspan="3"></td>
                    <td class="text-right">Nº OPERACIÓN:</td>
                    <td class="text-right">{{ $venta->numope }}</td>
                </tr>
                @endif
                @if((float)($venta->efectivo ?? 0) > 0)
                <tr>
                    <td colspan="3"></td>
                    <td class="text-right">EFECTIVO:</td>
                    <td class="text-right">{{ number_format($venta->efectivo, 2, '.', '') }}</td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td class="text-right">VUELTO:</td>
                    <td class="text-right">{{ number_format($venta->vuelto ?? 0, 2, '.', '') }}</td>
                </tr>
                @endif
            @endif
            <tr>
                <td colspan="5">SON: {{ $cantidadEnLetras }}</td>
            </tr>
        </table>
        <p class="text-center" style="margin-top: 12px; font-size: 0.9em;">Representación impresa de la Boleta/Factura de venta electrónica</p>
    </div>
    </div>
    <script>
        function cambiarFormato(f) {
            var u = '{{ $baseUrl }}&formato=' + f;
            window.location.href = u;
        }
    </script>
</body>
</html>
