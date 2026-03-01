@php
    $baseQuery = request()->only(['buscar', 'sort', 'direction']);
    $sortDir = fn ($col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? '↑' : '↓') : '';
@endphp
<div class="clientes-tabla-container">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'nombres', 'direction' => $sortDir('nombres')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Razón social {{ $sortIcon('nombres') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'direccion', 'direction' => $sortDir('direccion')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Dirección {{ $sortIcon('direccion') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'tipo_documento', 'direction' => $sortDir('tipo_documento')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Tipo doc. {{ $sortIcon('tipo_documento') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'nrodoc', 'direction' => $sortDir('nrodoc')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">N. documento {{ $sortIcon('nrodoc') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.clientes.index', array_merge($baseQuery, ['sort' => 'tipo', 'direction' => $sortDir('tipo')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Tipo {{ $sortIcon('tipo') }}</a>
                        </th>
                        <th class="px-5 py-3 text-right sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $c)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $c->nombres }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $c->direccion ?? '—' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $c->tipo_doc_descripcion ?? '—' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $c->nrodoc ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                @if($c->tipo === 'laboratorio')
                                    <x-ui.badge variant="light" color="primary" size="sm">Laboratorio</x-ui.badge>
                                @else
                                    <x-ui.badge variant="light" color="light" size="sm">Cliente</x-ui.badge>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('mantenimiento.clientes.edit', $c) }}">
                                        <x-ui.button type="button" variant="outline" size="sm" class="!py-2 !px-3 text-theme-xs">Editar</x-ui.button>
                                    </a>
                                    <form action="{{ route('mantenimiento.clientes.destroy', $c) }}" method="post" class="inline-block" x-data="{ loading: false }" @submit="loading = true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-theme-xs font-medium text-red-600 hover:bg-red-100 dark:border-red-800 dark:bg-red-500/15 dark:text-red-500 dark:hover:bg-red-500/25 disabled:opacity-50" :disabled="loading" onclick="return confirm('¿Realmente desea eliminar este registro?');">
                                            <span x-show="!loading">Eliminar</span>
                                            <span x-show="loading" x-cloak style="display: none;">Eliminando...</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400 sm:px-6">No hay registros que coincidan con la búsqueda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($clientes->hasPages())
        <div class="mt-4 flex flex-col items-center justify-between gap-2 border-t border-gray-100 pt-4 dark:border-gray-800 sm:flex-row sm:gap-4">
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} resultados
            </p>
            <nav class="flex items-center gap-1" aria-label="Paginación">
                @if ($clientes->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">‹</span>
                @else
                    <a href="{{ $clientes->withQueryString()->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">‹</a>
                @endif
                @foreach ($clientes->getUrlRange(max(1, $clientes->currentPage() - 2), min($clientes->lastPage(), $clientes->currentPage() + 2)) as $page => $url)
                    @if ($page == $clientes->currentPage())
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-500 bg-brand-500 text-theme-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-theme-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">{{ $page }}</a>
                    @endif
                @endforeach
                @if ($clientes->hasMorePages())
                    <a href="{{ $clientes->withQueryString()->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">›</a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">›</span>
                @endif
            </nav>
        </div>
    @endif
</div>
