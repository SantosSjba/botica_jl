@extends('layouts.app')

@section('content')
@php
    $imprimir = request()->boolean('imprimir');
@endphp
<div class="min-w-0 space-y-6 {{ $imprimir ? 'print:block' : '' }}">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-common.page-breadcrumb :pageTitle="$title" />
        @if(!$imprimir)
            <div class="flex items-center gap-2 print:hidden">
                <a href="{{ route('ventas.consulta.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Volver al listado</a>
                <a href="{{ url('/reportes/ticket?idventa=' . $venta->idventa) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Imprimir
                </a>
            </div>
        @endif
    </div>

    @php
        $serie = $venta->serie;
        $numeroComprobante = ($serie ? $serie->serie . '-' . str_pad((string)$serie->correlativo, 8, '0', STR_PAD_LEFT) : '—');
    @endphp
    <x-common.component-card :title="'Venta #' . $venta->idventa" :desc="($serie->serie ?? '') . ' ' . ($serie->correlativo ?? '') . ' — ' . ($venta->fecha_emision ? \Carbon\Carbon::parse($venta->fecha_emision)->format('d/m/Y') : '')">
        <div class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Cliente</p>
                    <p class="text-gray-800 dark:text-white/90">{{ $venta->cliente->nombres ?? '—' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fecha de emisión</p>
                    <p class="text-gray-800 dark:text-white/90">{{ $venta->fecha_emision ? \Carbon\Carbon::parse($venta->fecha_emision)->format('d/m/Y') : '—' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Comprobante</p>
                    <p class="text-gray-800 dark:text-white/90">{{ $numeroComprobante }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</p>
                    <p class="text-gray-800 dark:text-white/90">{{ $venta->estado ?? '—' }}</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full min-w-[500px] text-left text-sm">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">#</th>
                            <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Producto</th>
                            <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Presentación</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Cant.</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">P. unit.</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">Importe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->detalles as $index => $d)
                            <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-white/90">{{ $d->producto->descripcion ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $d->producto->presentacion->presentacion ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ (int) $d->cantidad }}</td>
                                <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ $simboloMoneda }} {{ number_format((float)$d->precio_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-800 dark:text-white/90">{{ $simboloMoneda }} {{ number_format((float)$d->importe_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end border-t border-gray-200 pt-4 dark:border-gray-700">
                <div class="w-full max-w-xs space-y-1 text-right">
                    <p class="flex justify-between text-sm text-gray-600 dark:text-gray-400"><span>Op. gravadas:</span> <span>{{ $simboloMoneda }} {{ number_format((float)$venta->op_gravadas, 2) }}</span></p>
                    <p class="flex justify-between text-sm text-gray-600 dark:text-gray-400"><span>IGV:</span> <span>{{ $simboloMoneda }} {{ number_format((float)$venta->igv, 2) }}</span></p>
                    <p class="flex justify-between text-base font-semibold text-gray-800 dark:text-white/90"><span>Total:</span> <span>{{ $simboloMoneda }} {{ number_format((float)$venta->total, 2) }}</span></p>
                </div>
            </div>
        </div>
    </x-common.component-card>
</div>
@endsection
