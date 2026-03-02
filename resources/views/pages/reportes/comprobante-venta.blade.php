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
        * { box-sizing: border-box; }
        body { margin: 0; padding: 12px; font-family: Arial, sans-serif; }
        .no-print { margin-bottom: 12px; }
        .no-print button { margin-right: 8px; padding: 8px 14px; cursor: pointer; }
        .comprobante { border: 1px solid #ccc; padding: 12px; }
        .comprobante table { width: 100%; border-collapse: collapse; }
        .comprobante th, .comprobante td { padding: 4px 6px; text-align: left; vertical-align: top; }
        .comprobante .text-right { text-align: right; }
        .comprobante .text-center { text-align: center; }
        .comprobante hr { border: none; border-top: 1px dashed #333; margin: 6px 0; }
        .comprobante .total-row { font-weight: bold; }

        /* Ticket: ancho reducido (~80mm / 302px) */
        .formato-ticket .comprobante { max-width: 302px; font-size: 11px; }
        .formato-ticket .comprobante img { max-width: 100px; max-height: 80px; }
        .formato-ticket .comprobante th, .formato-ticket .comprobante td { padding: 2px 4px; font-size: 11px; }

        /* A4 */
        .formato-a4 .comprobante { max-width: 210mm; font-size: 12px; }
        .formato-a4 .comprobante img { max-height: 80px; }

        /* A5 */
        .formato-a5 .comprobante { max-width: 148mm; font-size: 11px; }
        .formato-a5 .comprobante img { max-height: 70px; }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .comprobante { border: none; }
            .formato-ticket .comprobante { max-width: 80mm; }
            .formato-a4 .comprobante { max-width: 100%; }
            .formato-a5 .comprobante { max-width: 100%; }
        }
        @page { size: auto; margin: 10mm; }
        @page formato-ticket { size: 80mm auto; margin: 5mm; }
    </style>
</head>
<body class="formato-{{ $formato }}">
    <div class="no-print">
        <button type="button" onclick="cambiarFormato('ticket')">Imprimir Ticket</button>
        <button type="button" onclick="cambiarFormato('a4')">Imprimir A4</button>
        <button type="button" onclick="cambiarFormato('a5')">Imprimir A5</button>
        <button type="button" onclick="window.print()">Imprimir ahora</button>
    </div>
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
            <tr>
                <td colspan="5">SON: {{ $cantidadEnLetras }}</td>
            </tr>
        </table>
        <p class="text-center" style="margin-top: 12px; font-size: 0.9em;">Representación impresa de la Boleta/Factura de venta electrónica</p>
    </div>
    <script>
        function cambiarFormato(f) {
            var u = '{{ $baseUrl }}&formato=' + f;
            window.location.href = u;
        }
    </script>
</body>
</html>
