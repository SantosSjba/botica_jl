<div class="space-y-1 text-sm">
    <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-400">OP. GRAVADAS:</span>
        <span class="font-medium text-gray-800 dark:text-white/90">{{ $totales['simbolo_moneda'] }} {{ number_format($totales['op_gravadas'], 2) }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-400">IGV:</span>
        <span class="font-medium text-gray-800 dark:text-white/90">{{ $totales['simbolo_moneda'] }} {{ number_format($totales['igv'], 2) }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-400">OP. EXONERADAS:</span>
        <span class="font-medium text-gray-800 dark:text-white/90">{{ $totales['simbolo_moneda'] }} {{ number_format($totales['op_exoneradas'], 2) }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-400">OP. INAFECTAS:</span>
        <span class="font-medium text-gray-800 dark:text-white/90">{{ $totales['simbolo_moneda'] }} {{ number_format($totales['op_inafectas'], 2) }}</span>
    </div>
    <div class="flex justify-between border-t border-gray-200 pt-2 dark:border-gray-700">
        <span class="font-medium text-gray-700 dark:text-gray-300">Importe total:</span>
        <span class="font-bold text-gray-800 dark:text-white/90">{{ $totales['simbolo_moneda'] }} {{ number_format($totales['total'], 2) }}</span>
    </div>
</div>
