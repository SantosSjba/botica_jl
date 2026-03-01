@php
    $baseQuery = request()->only(['buscar', 'sort', 'direction']);
    $sortDir = fn ($col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
    $sortIcon = fn ($col) => $sort === $col ? ($direction === 'asc' ? '↑' : '↓') : '';
@endphp
<div class="usuarios-tabla-container">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[600px]">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.usuarios.index', array_merge($baseQuery, ['sort' => 'nombres', 'direction' => $sortDir('nombres')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Nombre {{ $sortIcon('nombres') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.usuarios.index', array_merge($baseQuery, ['sort' => 'telefono', 'direction' => $sortDir('telefono')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Teléfono {{ $sortIcon('telefono') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.usuarios.index', array_merge($baseQuery, ['sort' => 'fechaingreso', 'direction' => $sortDir('fechaingreso')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">F. ingreso {{ $sortIcon('fechaingreso') }}</a>
                        </th>
                        <th class="px-5 py-3 text-left sm:px-6">
                            <a href="{{ route('mantenimiento.usuarios.index', array_merge($baseQuery, ['sort' => 'estado', 'direction' => $sortDir('estado')])) }}" class="font-medium text-gray-500 text-theme-xs dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">Estado {{ $sortIcon('estado') }}</a>
                        </th>
                        <th class="px-5 py-3 text-right sm:px-6">
                            <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acciones</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $u)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">{{ $u->nombres }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $u->telefono ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $u->fechaingreso ? \Carbon\Carbon::parse($u->fechaingreso)->format('d/m/Y') : '—' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $u->estado ?? '—' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('mantenimiento.usuarios.edit', $u) }}">
                                        <x-ui.button type="button" variant="outline" size="sm" class="!py-2 !px-3 text-theme-xs">Editar</x-ui.button>
                                    </a>
                                    @if($u->puedeEliminar())
                                    <form action="{{ route('mantenimiento.usuarios.destroy', $u) }}" method="post" class="inline-block" x-data="{ loading: false }" @submit="loading = true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-theme-xs font-medium text-red-600 hover:bg-red-100 dark:border-red-800 dark:bg-red-500/15 dark:text-red-500 dark:hover:bg-red-500/25 disabled:opacity-50" :disabled="loading" onclick="return confirm('¿Realmente desea eliminar este usuario?');">
                                            <span x-show="!loading">Eliminar</span>
                                            <span x-show="loading" x-cloak style="display: none;">Eliminando...</span>
                                        </button>
                                    </form>
                                    @else
                                    <span class="inline-flex items-center rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-theme-xs text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400" title="{{ $u->mensajeNoEliminable() }}">Eliminar</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400 sm:px-6">No hay registros que coincidan con la búsqueda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($usuarios->hasPages())
        <div class="mt-4 flex flex-col items-center justify-between gap-2 border-t border-gray-100 pt-4 dark:border-gray-800 sm:flex-row sm:gap-4">
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} resultados
            </p>
            <nav class="flex items-center gap-1" aria-label="Paginación">
                @if ($usuarios->onFirstPage())
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">‹</span>
                @else
                    <a href="{{ $usuarios->withQueryString()->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">‹</a>
                @endif
                @foreach ($usuarios->getUrlRange(max(1, $usuarios->currentPage() - 2), min($usuarios->lastPage(), $usuarios->currentPage() + 2)) as $page => $url)
                    @if ($page == $usuarios->currentPage())
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-brand-500 bg-brand-500 text-theme-sm font-medium text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-theme-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">{{ $page }}</a>
                    @endif
                @endforeach
                @if ($usuarios->hasMorePages())
                    <a href="{{ $usuarios->withQueryString()->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">›</a>
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">›</span>
                @endif
            </nav>
        </div>
    @endif
</div>
