@php
    $baseQuery = request()->only(['fecha_desde', 'fecha_hasta', 'sort', 'direction']);
    $sortDir = fn ($col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? '↑' : '↓') : '';
    $reporteTicketUrl = url('/reportes/ticket');
@endphp
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[800px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left sm:px-6">
                        <a href="{{ route('ventas.consulta.index', array_merge($baseQuery, ['sort' => 'idventa', 'direction' => $sortDir('idventa')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">ID {{ $sortIcon('idventa') }}</a>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <a href="{{ route('ventas.consulta.index', array_merge($baseQuery, ['sort' => 'fecha_emision', 'direction' => $sortDir('fecha_emision')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Fecha {{ $sortIcon('fecha_emision') }}</a>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <a href="{{ route('ventas.consulta.index', array_merge($baseQuery, ['sort' => 'serie', 'direction' => $sortDir('serie')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Serie {{ $sortIcon('serie') }}</a>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <a href="{{ route('ventas.consulta.index', array_merge($baseQuery, ['sort' => 'correlativo', 'direction' => $sortDir('correlativo')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Correl. {{ $sortIcon('correlativo') }}</a>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">Estado</th>
                    <th class="px-5 py-3 text-left sm:px-6">Rpt. SUNAT</th>
                    <th class="px-5 py-3 text-right sm:px-6">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ventas as $v)
                    @php
                        $estado = $v->estado ?? 'no_enviado';
                        $estadoClass = match($estado) {
                            'enviado' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500',
                            'anulado' => 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-500',
                            default => 'bg-gray-100 text-gray-700 dark:bg-gray-500/15 dark:text-gray-400',
                        };
                        $feMsg = $v->femensajesunat ?? '—';
                        $feClass = ($feMsg === 'Aceptada') ? 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-500' : (($feMsg === 'Observaciones') ? 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400');
                    @endphp
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-5 py-4 text-theme-sm text-gray-800 dark:text-white/90 sm:px-6">{{ $v->idventa }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-400 sm:px-6">{{ $v->fecha_emision ? \Carbon\Carbon::parse($v->fecha_emision)->format('d/m/Y') : '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-theme-sm text-gray-800 dark:text-white/90 sm:px-6">{{ $v->serie->serie ?? '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-theme-sm text-gray-800 dark:text-white/90 sm:px-6">{{ $v->serie->correlativo ?? '—' }}</td>
                        <td class="px-5 py-4 sm:px-6">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-theme-xs font-medium {{ $estadoClass }}">{{ $estado }}</span>
                        </td>
                        <td class="px-5 py-4 sm:px-6">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-theme-xs font-medium {{ $feClass }}">{{ $feMsg }}</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('ventas.consulta.show', $v->idventa) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Ver</a>
                                <a href="{{ $reporteTicketUrl }}?idventa={{ $v->idventa }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-theme-xs font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    Imprimir
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400 sm:px-6">No hay ventas (facturas/boletas) que coincidan con los filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ventas->hasPages())
        <div class="mt-4 flex flex-col items-center justify-between gap-2 border-t border-gray-100 pt-4 dark:border-gray-800 sm:flex-row sm:gap-4">
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                Mostrando {{ $ventas->firstItem() }} a {{ $ventas->lastItem() }} de {{ $ventas->total() }} resultados
            </p>
            <nav class="flex items-center gap-1" aria-label="Paginación">
                @if ($ventas->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">‹</span>
                @else
                    <a href="{{ $ventas->withQueryString()->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">‹</a>
                @endif
                @foreach ($ventas->getUrlRange(max(1, $ventas->currentPage() - 2), min($ventas->lastPage(), $ventas->currentPage() + 2)) as $page => $url)
                    @if ($page == $ventas->currentPage())
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-500 bg-brand-500 text-theme-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-theme-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">{{ $page }}</a>
                    @endif
                @endforeach
                @if ($ventas->hasMorePages())
                    <a href="{{ $ventas->withQueryString()->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">›</a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">›</span>
                @endif
            </nav>
        </div>
    @endif
</div>
