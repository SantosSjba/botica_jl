<div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="w-full min-w-[700px]">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Item</p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Descripción</p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Presentación</p>
                    </th>
                    <th class="px-5 py-3 text-left sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Cant.</p>
                    </th>
                    <th class="px-5 py-3 text-right sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">V. unit.</p>
                    </th>
                    <th class="px-5 py-3 text-right sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">P. unit.</p>
                    </th>
                    <th class="px-5 py-3 text-right sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Importe</p>
                    </th>
                    <th class="px-5 py-3 w-12 text-center sm:px-6">
                        <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400"></p>
                    </th>
                </tr>
            </thead>
            <tbody id="ventas-carrito-tbody">
                @forelse($items as $index => $row)
                    <tr class="border-b border-gray-100 dark:border-gray-800" data-idproducto="{{ $row->idproducto }}">
                        <td class="whitespace-nowrap px-5 py-4 text-theme-sm text-gray-800 dark:text-white/90 sm:px-6">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 text-theme-sm text-gray-800 dark:text-white/90 sm:px-6">{{ $row->descripcion }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-theme-sm text-gray-600 dark:text-gray-400 sm:px-6">{{ $row->presentacion ?? '—' }}</td>
                        <td class="whitespace-nowrap px-5 py-4 sm:px-6">
                            <input type="number" class="ventas-cantidad-input h-9 w-20 rounded-lg border border-gray-300 bg-white px-2 text-center text-theme-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white/90" min="1" step="1" value="{{ (int) $row->cantidad }}" data-id="{{ $row->idproducto }}" title="Editar cantidad (entero)" />
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right text-theme-sm text-gray-600 dark:text-gray-400 sm:px-6">{{ number_format((float)$row->valor_unitario, 2) }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-right sm:px-6">
                            <span class="ventas-precio editable-precio cursor-pointer rounded px-1.5 py-0.5 text-theme-sm hover:bg-gray-100 dark:hover:bg-gray-700" data-id="{{ $row->idproducto }}" contenteditable="true">{{ number_format((float)$row->precio_unitario, 2) }}</span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4 text-right text-theme-sm font-medium text-gray-800 dark:text-white/90 sm:px-6">{{ $simboloMoneda }} {{ number_format((float)$row->importe_total, 2) }}</td>
                        <td class="whitespace-nowrap px-5 py-4 text-center sm:px-6">
                            <button type="button" class="ventas-btn-quitar inline-flex h-8 w-8 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50" data-id="{{ $row->idproducto }}" title="Quitar">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-6 text-center text-theme-sm text-gray-500 dark:text-gray-400 sm:px-6">No hay productos en el carrito. Use el código de barras o Buscar productos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
