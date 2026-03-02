@extends('layouts.app')

@section('content')
@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card title="Reporte ventas del día" desc="Ventas de un día. Detalle por ítem. Excluye anuladas.">
        <div class="space-y-4">
            <form method="get" action="{{ route('reportes.ventas.dia') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label for="fecha" class="{{ $labelClass }}">Fecha</label>
                    <input type="date" id="fecha" name="fecha" value="{{ $fecha }}" class="{{ $inputClass }}" />
                </div>
                <x-ui.button type="submit" variant="primary" size="md" class="shrink-0">Ver reporte</x-ui.button>
            </form>

            <p class="text-sm text-gray-600 dark:text-gray-400"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($fecha)->locale('es')->translatedFormat('d/m/Y') }}</p>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full min-w-[600px] text-left text-sm text-gray-700 dark:text-gray-300">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Fecha</th>
                            <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Producto</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Cant.</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">P. unit.</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Subtotal</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Total venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $idventaAnt = 0; @endphp
                        @forelse($filas as $row)
                            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                <td class="px-4 py-3">{{ $row->fecha_emision }}</td>
                                <td class="max-w-[200px] truncate px-4 py-3 text-gray-800 dark:text-white/90" title="{{ e($row->producto) }}">{{ $row->producto }}</td>
                                <td class="px-4 py-3 text-right">{{ $row->cantidad }}</td>
                                <td class="px-4 py-3 text-right">{{ $simboloMoneda }} {{ number_format((float)($row->precio_unitario ?? $row->valor_unitario), 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ $simboloMoneda }} {{ number_format((float)$row->importe_total, 2) }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if($idventaAnt != $row->idventa)
                                        {{ $simboloMoneda }} {{ number_format((float)$row->total, 2) }}
                                        @php $idventaAnt = $row->idventa; @endphp
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hay ventas en la fecha seleccionada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-right text-base font-semibold text-gray-800 dark:text-white/90">Total ventas: {{ $simboloMoneda }} {{ number_format($totalVentas, 2) }}</p>

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <button type="button" onclick="window.print();" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Imprimir
                </button>
            </div>
        </div>
    </x-common.component-card>
</div>
@endsection
