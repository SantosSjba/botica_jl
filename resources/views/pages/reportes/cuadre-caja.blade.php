@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb pageTitle="Cuadre diario de caja" />

    <x-common.component-card title="Cuadre diario de caja" :desc="'Usuario: ' . $usuario . ' — Fecha: ' . $fecha">
        <div class="space-y-4 text-sm">
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-gray-500 dark:text-gray-400">Caja / Turno</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">{{ $apertura->caja }} — {{ $apertura->turno }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-gray-500 dark:text-gray-400">Monto apertura</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">S/ {{ number_format((float)$apertura->monto, 2) }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-gray-500 dark:text-gray-400">Total ventas</p>
                    <p class="font-medium text-gray-800 dark:text-white/90">S/ {{ number_format($totalVentas, 2) }}</p>
                </div>
            </div>

            <div>
                <p class="mb-2 font-medium text-gray-700 dark:text-gray-300">Ventas por forma de pago</p>
                <ul class="list-inside list-disc space-y-1 text-gray-600 dark:text-gray-400">
                    @foreach($porForma as $forma => $monto)
                        <li>{{ $forma }}: S/ {{ number_format($monto, 2) }}</li>
                    @endforeach
                    @if(empty($porForma))
                        <li class="text-gray-500">Sin ventas</li>
                    @endif
                </ul>
            </div>

            @if($cierre)
                <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                    <p class="mb-2 font-medium text-gray-700 dark:text-gray-300">Cierre registrado</p>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <p class="text-gray-600 dark:text-gray-400">Caja sistema: S/ {{ number_format((float)$cierre->caja_sistema, 2) }}</p>
                        <p class="text-gray-600 dark:text-gray-400">Efectivo en caja: S/ {{ number_format((float)$cierre->efectivo_caja, 2) }}</p>
                        <p class="text-gray-600 dark:text-gray-400">Diferencia: S/ {{ number_format((float)$cierre->diferencia, 2) }}</p>
                    </div>
                </div>
            @endif

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <a href="{{ route('caja.seguimiento') }}"><x-ui.button type="button" variant="outline" size="md">Volver a seguimiento</x-ui.button></a>
                <button type="button" onclick="window.print();" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Imprimir
                </button>
            </div>
        </div>
    </x-common.component-card>
</div>
@endsection
