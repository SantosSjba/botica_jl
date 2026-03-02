@php
    $baseQuery = request()->only(['buscar', 'fecha_desde', 'fecha_hasta', 'docu', 'sort', 'direction']);
    $sortDir = fn ($col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? '↑' : '↓') : '';
@endphp
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[700px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left sm:px-6">
                        <a href="{{ route('compras.consulta.index', array_merge($baseQuery, ['sort' => 'docu', 'direction' => $sortDir('docu')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Documento {{ $sortIcon('docu') }}</a>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <a href="{{ route('compras.consulta.index', array_merge($baseQuery, ['sort' => 'num_docu', 'direction' => $sortDir('num_docu')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Número {{ $sortIcon('num_docu') }}</a>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <a href="{{ route('compras.consulta.index', array_merge($baseQuery, ['sort' => 'fecha', 'direction' => $sortDir('fecha')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Fecha {{ $sortIcon('fecha') }}</a>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">Proveedor</th>
                    <th class="px-5 py-3 text-right sm:px-6">Subtotal</th>
                    <th class="px-5 py-3 text-right sm:px-6">IGV</th>
                    <th class="px-5 py-3 text-right sm:px-6">
                        <a href="{{ route('compras.consulta.index', array_merge($baseQuery, ['sort' => 'total', 'direction' => $sortDir('total')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Total {{ $sortIcon('total') }}</a>
                    </th>
                    <th class="px-5 py-3 text-right sm:px-6">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($compras as $c)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-5 py-4 sm:px-6">
                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $c->docu }}</p>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <p class="text-gray-700 text-theme-sm dark:text-gray-300">{{ $c->num_docu }}</p>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <p class="text-gray-600 text-theme-sm dark:text-gray-400">{{ $c->fecha->format('d/m/Y') }}</p>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <p class="text-gray-600 text-theme-sm dark:text-gray-400">{{ $c->proveedor->nombres ?? '—' }}</p>
                        </td>
                        <td class="px-5 py-4 text-right sm:px-6">
                            <p class="text-theme-sm text-gray-700 dark:text-gray-300">S/ {{ number_format((float)$c->subtotal, 2) }}</p>
                        </td>
                        <td class="px-5 py-4 text-right sm:px-6">
                            <p class="text-theme-sm text-gray-700 dark:text-gray-300">S/ {{ number_format((float)$c->igv, 2) }}</p>
                        </td>
                        <td class="px-5 py-4 text-right sm:px-6">
                            <p class="font-medium text-theme-sm text-gray-800 dark:text-white/90">S/ {{ number_format((float)$c->total, 2) }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('compras.consulta.show', $c->idcompra) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Ver</a>
                                <a href="{{ route('compras.consulta.show', $c->idcompra) }}?imprimir=1" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    Imprimir
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400 sm:px-6">No hay compras que coincidan con los filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($compras->hasPages())
        <div class="mt-4 flex flex-col items-center justify-between gap-2 border-t border-gray-100 pt-4 dark:border-gray-800 sm:flex-row sm:gap-4">
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                Mostrando {{ $compras->firstItem() }} a {{ $compras->lastItem() }} de {{ $compras->total() }} resultados
            </p>
            <nav class="flex items-center gap-1" aria-label="Paginación">
                @if ($compras->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">‹</span>
                @else
                    <a href="{{ $compras->withQueryString()->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">‹</a>
                @endif
                @foreach ($compras->getUrlRange(max(1, $compras->currentPage() - 2), min($compras->lastPage(), $compras->currentPage() + 2)) as $page => $url)
                    @if ($page == $compras->currentPage())
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-500 bg-brand-500 text-theme-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-theme-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">{{ $page }}</a>
                    @endif
                @endforeach
                @if ($compras->hasMorePages())
                    <a href="{{ $compras->withQueryString()->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">›</a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">›</span>
                @endif
            </nav>
        </div>
    @endif
</div>
