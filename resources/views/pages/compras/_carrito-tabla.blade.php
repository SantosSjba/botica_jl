<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
    <table class="w-full min-w-[600px] text-left text-sm text-gray-700 dark:text-gray-300">
        <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
            <tr>
                <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">#</th>
                <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Descripción</th>
                <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Presentación</th>
                <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Cantidad</th>
                <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">P. compra</th>
                <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Importe</th>
                <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Quitar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr class="border-b border-gray-100 dark:border-gray-700/50" data-idproducto="{{ $item->idproducto }}">
                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 text-gray-800 dark:text-white/90">{{ $item->descripcion }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $item->presentacion }}</td>
                    <td class="px-4 py-3 text-right">
                        <input type="number" min="1" step="1" value="{{ $item->cantidad }}"
                            class="compras-cantidad w-20 rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-right text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white/90" />
                    </td>
                    <td class="px-4 py-3 text-right">
                        <input type="number" min="0" step="0.01" value="{{ number_format((float)$item->precio, 2, '.', '') }}"
                            class="compras-precio w-24 rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-right text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white/90" />
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">S/ {{ number_format((float)$item->importe, 2) }}</td>
                    <td class="px-4 py-3 text-right">
                        <button type="button" class="compras-quitar inline-flex rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-100 dark:border-red-800 dark:bg-red-500/15 dark:text-red-500 dark:hover:bg-red-500/25" data-idproducto="{{ $item->idproducto }}" title="Quitar del carrito">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hay productos en el carrito. Busque y agregue productos arriba.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
