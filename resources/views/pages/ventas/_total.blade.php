<div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/50">
    <p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Total a pagar</p>
    <p class="text-2xl font-bold text-gray-800 dark:text-white/90">
        {{ $totales['simbolo_moneda'] }} {{ number_format($totales['total'], 2) }}
    </p>
    <input type="hidden" id="ventas-total-input" name="total" value="{{ number_format($totales['total'], 2, '.', '') }}" />
</div>
