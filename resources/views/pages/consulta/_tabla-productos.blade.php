@php
    $baseQuery = request()->only(['buscar', 'sort', 'direction']);
    $sortDir = fn ($col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? '↑' : '↓') : '';
@endphp
<div class="min-w-0 w-full overflow-x-auto">
    <table class="min-w-full">
        <thead class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400 sm:pl-6">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'codigo', 'direction' => $sortDir('codigo')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Código {{ $sortIcon('codigo') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'descripcion', 'direction' => $sortDir('descripcion')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Descripción {{ $sortIcon('descripcion') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'presentacion', 'direction' => $sortDir('presentacion')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Presentación {{ $sortIcon('presentacion') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'fecha_vencimiento', 'direction' => $sortDir('fecha_vencimiento')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Fec. venc {{ $sortIcon('fecha_vencimiento') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'stock', 'direction' => $sortDir('stock')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Stock {{ $sortIcon('stock') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'precio_venta', 'direction' => $sortDir('precio_venta')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">P. venta {{ $sortIcon('precio_venta') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'tipo', 'direction' => $sortDir('tipo')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Tipo {{ $sortIcon('tipo') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'estado', 'direction' => $sortDir('estado')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Estado {{ $sortIcon('estado') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'sintoma', 'direction' => $sortDir('sintoma')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Síntomas {{ $sortIcon('sintoma') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'lote', 'direction' => $sortDir('lote')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Lote {{ $sortIcon('lote') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'descuento', 'direction' => $sortDir('descuento')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Descuento {{ $sortIcon('descuento') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('consulta.productos', array_merge($baseQuery, ['sort' => 'ventasujeta', 'direction' => $sortDir('ventasujeta')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Venta sujeta {{ $sortIcon('ventasujeta') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400 pr-4 sm:pr-6">Similar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $p)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-800 dark:text-white/90 sm:pl-6">{{ $p->codigo }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-white/90">{{ $p->descripcion }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->presentacion_nombre ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->fecha_vencimiento ? \Carbon\Carbon::parse($p->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3">
                        @if($p->stock <= ($p->stockminimo ?? 0))
                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ $p->stock }}</span>
                        @else
                            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ $p->stock }}</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $simboloMoneda }} {{ number_format((float)$p->precio_venta, 2) }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->tipo ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3">
                        @if($p->estado == '1')
                            <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">Activo</span>
                        @else
                            <span class="rounded-full bg-error-50 px-2 py-0.5 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->sintoma_nombre ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->lote_numero ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->descuento ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ ($p->ventasujeta ?? '') === 'Con receta medica' ? 'Sí' : 'No' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 pr-4 sm:pr-6">
                        <a href="{{ route('en-desarrollo') }}?idproducto={{ $p->idproducto }}" class="inline-flex items-center rounded-lg bg-success-50 px-2.5 py-1.5 text-xs font-medium text-success-600 hover:bg-success-100 dark:bg-success-500/15 dark:text-success-500 dark:hover:bg-success-500/25">Similar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:pl-6">No hay productos que coincidan con la búsqueda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($productos->hasPages())
    <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-800 sm:px-6">
        <div class="flex flex-col items-center justify-between gap-2 sm:flex-row sm:gap-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }} resultados
            </p>
            <nav class="flex items-center gap-1" aria-label="Paginación">
                @if ($productos->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">‹</span>
                @else
                    <a href="{{ $productos->withQueryString()->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">‹</a>
                @endif
                @foreach ($productos->getUrlRange(max(1, $productos->currentPage() - 2), min($productos->lastPage(), $productos->currentPage() + 2)) as $page => $url)
                    @if ($page == $productos->currentPage())
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-500 bg-brand-500 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">{{ $page }}</a>
                    @endif
                @endforeach
                @if ($productos->hasMorePages())
                    <a href="{{ $productos->withQueryString()->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">›</a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">›</span>
                @endif
            </nav>
        </div>
    </div>
@endif
