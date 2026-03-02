<div class="grid grid-cols-1 gap-4 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50 sm:grid-cols-3">
    <div class="flex flex-col justify-center rounded-lg border border-gray-200 bg-white/60 px-4 py-3 dark:border-gray-600 dark:bg-white/[0.04]">
        <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Subtotal</span>
        <span class="mt-0.5 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $simboloMoneda }} {{ number_format($subtotal, 2) }}</span>
    </div>
    <div class="flex flex-col justify-center rounded-lg border border-gray-200 bg-white/60 px-4 py-3 dark:border-gray-600 dark:bg-white/[0.04]">
        <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">IGV</span>
        <span class="mt-0.5 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $simboloMoneda }} {{ number_format($igv, 2) }}</span>
    </div>
    <div class="flex flex-col justify-center rounded-lg border-2 border-brand-200 bg-brand-50/80 px-4 py-3 dark:border-brand-700 dark:bg-brand-500/10">
        <span class="text-xs font-medium uppercase tracking-wide text-brand-600 dark:text-brand-400">Total</span>
        <span class="mt-0.5 text-xl font-bold text-brand-700 dark:text-brand-300">{{ $simboloMoneda }} {{ number_format($total, 2) }}</span>
    </div>
</div>
