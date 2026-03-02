<div class="max-h-[55vh] overflow-auto custom-scrollbar">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="max-w-full overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[640px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Descripción</p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Presentación</p>
                    </th>
                    <th class="px-5 py-3 text-right sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Precio</p>
                    </th>
                    <th class="px-5 py-3 text-center sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Stock</p>
                    </th>
                    <th class="px-5 py-3 text-center sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Cant.</p>
                    </th>
                    <th class="px-5 py-3 text-center sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Acción</p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $p)
                    @php $stock = (int) $p->stock; $maxCant = $stock > 0 ? $stock : 1; @endphp
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-5 py-4 sm:px-6">
                            <p class="text-gray-800 text-theme-sm dark:text-white/90">{{ $p->descripcion }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                            <p class="text-gray-500 text-theme-sm dark:text-gray-400">{{ $p->presentacion_nombre ?? '—' }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                            <p class="text-gray-800 text-theme-sm dark:text-white/90">{{ $simboloMoneda }} {{ number_format((float)$p->precio_venta, 2) }}</p>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-center sm:px-6">
                            @if($stock <= 0)
                                <span class="inline-block rounded-full bg-red-50 px-2 py-0.5 text-theme-xs font-medium text-red-700 dark:bg-red-500/15 dark:text-red-500">{{ $stock }}</span>
                            @else
                                <span class="inline-block rounded-full bg-green-50 px-2 py-0.5 text-theme-xs font-medium text-green-700 dark:bg-green-500/15 dark:text-green-500">{{ $stock }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-center sm:px-6">
                            <input type="number" class="ventas-modal-cantidad h-9 w-16 rounded-lg border border-gray-300 bg-white px-2 text-center text-theme-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white/90" min="1" max="{{ $maxCant }}" step="1" value="1" data-max="{{ $maxCant }}" {{ $stock <= 0 ? 'disabled' : '' }} title="Cantidad a agregar (máx. {{ $maxCant }})" />
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-center sm:px-6">
                            <button type="button" class="ventas-modal-btn-agregar inline-flex items-center gap-1 rounded-lg bg-brand-500 px-2.5 py-1.5 text-theme-xs font-medium text-white hover:bg-brand-600 disabled:opacity-50" data-idproducto="{{ $p->idproducto }}" data-des="{{ e($p->descripcion) }}" data-pres="{{ e($p->presentacion_nombre ?? '') }}" data-pre="{{ number_format((float)$p->precio_venta, 2, '.', '') }}" {{ $stock <= 0 ? 'disabled' : '' }}>
                                Agregar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-theme-sm text-gray-500 dark:text-gray-400 sm:px-6">No hay productos que coincidan con la búsqueda.</td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>

@if($productos->hasPages())
    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-gray-200 pt-3 dark:border-gray-700">
        <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
            Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }} resultados
        </p>
        <nav class="flex items-center gap-1">
            @if ($productos->onFirstPage())
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500">‹</span>
            @else
                <a href="{{ $productos->withQueryString()->previousPageUrl() }}" class="ventas-modal-pagina inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">‹</a>
            @endif
            @foreach ($productos->getUrlRange(max(1, $productos->currentPage() - 1), min($productos->lastPage(), $productos->currentPage() + 1)) as $page => $url)
                @if ($page == $productos->currentPage())
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-brand-500 bg-brand-500 text-sm font-medium text-white">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="ventas-modal-pagina inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">{{ $page }}</a>
                @endif
            @endforeach
            @if ($productos->hasMorePages())
                <a href="{{ $productos->withQueryString()->nextPageUrl() }}" class="ventas-modal-pagina inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">›</a>
            @else
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500">›</span>
            @endif
        </nav>
    </div>
@endif
