@php
    $baseQuery = request()->only(['buscar', 'sort', 'direction']);
    $sortDir = fn ($col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? '↑' : '↓') : '';
@endphp
<div class="productos-tabla-container">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[900px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.productos.index', array_merge($baseQuery, ['sort' => 'codigo', 'direction' => $sortDir('codigo')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Código {{ $sortIcon('codigo') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.productos.index', array_merge($baseQuery, ['sort' => 'descripcion', 'direction' => $sortDir('descripcion')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Descripción {{ $sortIcon('descripcion') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.productos.index', array_merge($baseQuery, ['sort' => 'presentacion', 'direction' => $sortDir('presentacion')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Presentación {{ $sortIcon('presentacion') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.productos.index', array_merge($baseQuery, ['sort' => 'stock', 'direction' => $sortDir('stock')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Stock {{ $sortIcon('stock') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.productos.index', array_merge($baseQuery, ['sort' => 'precio_venta', 'direction' => $sortDir('precio_venta')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">P. venta {{ $sortIcon('precio_venta') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.productos.index', array_merge($baseQuery, ['sort' => 'estado', 'direction' => $sortDir('estado')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Estado {{ $sortIcon('estado') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.productos.index', array_merge($baseQuery, ['sort' => 'tipo', 'direction' => $sortDir('tipo')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Tipo {{ $sortIcon('tipo') }}</a>
                        </th>
                        <th class="px-5 py-3 text-right sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $p)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $p->codigo ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $p->descripcion }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $p->presentacion_nombre ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                @if($p->stock <= ($p->stockminimo ?? 0))
                                    <x-ui.badge variant="light" color="error" size="sm">{{ $p->stock }}</x-ui.badge>
                                @else
                                    <x-ui.badge variant="light" color="success" size="sm">{{ $p->stock }}</x-ui.badge>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $simboloMoneda }} {{ number_format((float)$p->precio_venta, 2) }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                @if($p->estado == '1')
                                    <x-ui.badge variant="light" color="success" size="sm">Activo</x-ui.badge>
                                @else
                                    <x-ui.badge variant="light" color="error" size="sm">Inactivo</x-ui.badge>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                                <x-ui.badge variant="light" color="info" size="sm">{{ $p->tipo ?? '—' }}</x-ui.badge>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('mantenimiento.productos.edit', $p) }}">
                                        <x-ui.button type="button" variant="outline" size="sm" class="!py-2 !px-3 text-theme-xs">Editar</x-ui.button>
                                    </a>
                                    <form action="{{ route('mantenimiento.productos.destroy', $p) }}" method="post" class="form-ajax-submit inline-block" data-confirm="¿Realmente desea eliminar este producto?" x-data="{ loading: false }" @submit="loading = true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-theme-xs font-medium text-red-600 hover:bg-red-100 dark:border-red-800 dark:bg-red-500/15 dark:text-red-500 dark:hover:bg-red-500/25 disabled:opacity-50" :disabled="loading">
                                            <span x-show="!loading">Eliminar</span>
                                            <span x-show="loading" x-cloak style="display: none;">Eliminando...</span>
                                        </button>
                                    </form>
                                    <a href="{{ route('en-desarrollo') }}?idproducto={{ $p->idproducto }}">
                                        <x-ui.button type="button" variant="outline" size="sm" class="!py-2 !px-3 text-theme-xs">Similar</x-ui.button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400 sm:px-6">No hay productos que coincidan con la búsqueda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($productos->hasPages())
        <div class="mt-4 flex flex-col items-center justify-between gap-2 border-t border-gray-100 pt-4 dark:border-gray-800 sm:flex-row sm:gap-4">
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }} resultados
            </p>
            <nav class="flex items-center gap-1" aria-label="Paginación">
                @if ($productos->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">‹</span>
                @else
                    <a href="{{ $productos->withQueryString()->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">‹</a>
                @endif
                @foreach ($productos->getUrlRange(max(1, $productos->currentPage() - 2), min($productos->lastPage(), $productos->currentPage() + 2)) as $page => $url)
                    @if ($page == $productos->currentPage())
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-500 bg-brand-500 text-theme-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-theme-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">{{ $page }}</a>
                    @endif
                @endforeach
                @if ($productos->hasMorePages())
                    <a href="{{ $productos->withQueryString()->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">›</a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">›</span>
                @endif
            </nav>
        </div>
    @endif
</div>
