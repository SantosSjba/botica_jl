@php
    $baseQuery = request()->only(['buscar', 'sort', 'direction']);
    $sortDir = fn ($col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? '↑' : '↓') : '';
@endphp
<div class="clientes-tabla-container">
<div class="min-w-0 w-full overflow-x-auto">
    <table class="min-w-full">
        <thead class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400 sm:pl-6">
                    <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'nombres', 'direction' => $sortDir('nombres')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Razón social {{ $sortIcon('nombres') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'direccion', 'direction' => $sortDir('direccion')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Dirección {{ $sortIcon('direccion') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'tipo_documento', 'direction' => $sortDir('tipo_documento')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Tipo doc. {{ $sortIcon('tipo_documento') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'nrodoc', 'direction' => $sortDir('nrodoc')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">N. documento {{ $sortIcon('nrodoc') }}</a>
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">
                    <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'tipo', 'direction' => $sortDir('tipo')])) }}" class="inline-flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-300">Tipo {{ $sortIcon('tipo') }}</a>
                </th>
                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400 pr-4 sm:pr-6">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clientes as $c)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-white/90 sm:pl-6">{{ $c->nombres }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $c->direccion ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $c->tipo_doc_descripcion ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $c->nrodoc ?? '—' }}</td>
                    <td class="whitespace-nowrap px-4 py-3">
                        @if($c->tipo === 'laboratorio')
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Laboratorio</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cliente</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-4 py-3 text-right pr-4 sm:pr-6">
                        <a href="{{ route('mantenimiento.clientes.edit', $c) }}" class="inline-flex items-center rounded-lg bg-brand-50 px-2.5 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-500/15 dark:text-brand-500 dark:hover:bg-brand-500/25">Editar</a>
                        <form action="{{ route('mantenimiento.clientes.destroy', $c) }}" method="post" class="inline-block" x-data="{ loading: false }" @submit="loading = true">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ml-1 inline-flex items-center rounded-lg bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 dark:bg-red-500/15 dark:text-red-500 dark:hover:bg-red-500/25" :disabled="loading" onclick="return confirm('¿Realmente desea eliminar este registro?');">
                                <span x-show="!loading">Eliminar</span>
                                <span x-show="loading" x-cloak style="display: none;">Eliminando...</span>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400 sm:pl-6">No hay registros que coincidan con la búsqueda.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($clientes->hasPages())
    <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-800 sm:px-6">
        <div class="flex flex-col items-center justify-between gap-2 sm:flex-row sm:gap-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} resultados
            </p>
            <nav class="flex items-center gap-1" aria-label="Paginación">
                @if ($clientes->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">‹</span>
                @else
                    <a href="{{ $clientes->withQueryString()->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">‹</a>
                @endif
                @foreach ($clientes->getUrlRange(max(1, $clientes->currentPage() - 2), min($clientes->lastPage(), $clientes->currentPage() + 2)) as $page => $url)
                    @if ($page == $clientes->currentPage())
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-500 bg-brand-500 text-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">{{ $page }}</a>
                    @endif
                @endforeach
                @if ($clientes->hasMorePages())
                    <a href="{{ $clientes->withQueryString()->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">›</a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">›</span>
                @endif
            </nav>
        </div>
    </div>
@endif
</div>
