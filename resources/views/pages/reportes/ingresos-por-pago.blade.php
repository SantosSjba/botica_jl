@extends('layouts.app')

@section('content')
@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card title="Ingresos por tipo de pago" desc="Total de ventas agrupado por forma de pago en el período. Incluye ventas con un solo pago y ventas con múltiples pagos (ej. YAPE + EFECTIVO). Excluye anuladas.">
        <div class="space-y-4">
            <form method="get" action="{{ route('reportes.ingresos-por-pago') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="desde" class="{{ $labelClass }}">Desde</label>
                    <input type="date" id="desde" name="desde" value="{{ $desde }}" class="{{ $inputClass }}" />
                </div>
                <div>
                    <label for="hasta" class="{{ $labelClass }}">Hasta</label>
                    <input type="date" id="hasta" name="hasta" value="{{ $hasta }}" class="{{ $inputClass }}" />
                </div>
                <x-ui.button type="submit" variant="primary" size="md" class="shrink-0">Ver reporte</x-ui.button>
            </form>

            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Período:</strong> {{ \Carbon\Carbon::parse($desde)->locale('es')->translatedFormat('d/m/Y') }} – {{ \Carbon\Carbon::parse($hasta)->locale('es')->translatedFormat('d/m/Y') }}</p>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full min-w-[320px] text-left text-sm text-gray-700 dark:text-gray-300">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Tipo de pago</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($porTipo as $tipo => $total)
                            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                <td class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">{{ $tipo }}</td>
                                <td class="px-4 py-3 text-right">{{ $simboloMoneda }} {{ number_format($total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No hay datos en el período.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(!empty($porTipo))
                    <tfoot class="border-t-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                        <tr>
                            <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white/90">Total general</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white/90">{{ $simboloMoneda }} {{ number_format($totalGeneral, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </x-common.component-card>
</div>
@endsection
