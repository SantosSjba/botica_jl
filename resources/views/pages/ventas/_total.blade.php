<div>
    <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total a pagar</p>
    <p class="text-xl font-bold text-gray-800 dark:text-white/90">
        {{ $totales['simbolo_moneda'] }} {{ number_format($totales['total'], 2) }}
    </p>
    <input type="hidden" id="ventas-total-input" name="total" value="{{ number_format($totales['total'], 2, '.', '') }}" />
</div>
