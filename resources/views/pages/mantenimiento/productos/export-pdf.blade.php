<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Productos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { font-size: 16px; margin-bottom: 8px; }
        .fecha { color: #666; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
        th { background-color: #e5e7eb; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9fafb; }
        .num { text-align: right; }
        .footer { margin-top: 16px; font-size: 9px; color: #666; }
    </style>
</head>
<body>
    <h1>Listado de Productos</h1>
    <p class="fecha">Generado: {{ $fecha }}</p>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Presentación</th>
                <th class="num">Stock</th>
                <th class="num">P. venta</th>
                <th>Estado</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $p)
                <tr>
                    <td>{{ $p->codigo ?? '—' }}</td>
                    <td>{{ $p->descripcion }}</td>
                    <td>{{ $p->presentacion_nombre ?? '—' }}</td>
                    <td class="num">{{ $p->stock ?? 0 }}</td>
                    <td class="num">{{ $simboloMoneda }} {{ number_format((float)($p->precio_venta ?? 0), 2) }}</td>
                    <td>{{ $p->estado == '1' ? 'Activo' : 'Inactivo' }}</td>
                    <td>{{ $p->tipo ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No hay productos que exportar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <p class="footer">Total: {{ $productos->count() }} producto(s).</p>
</body>
</html>
