@extends('layouts.app')

@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-9 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $inputReadonlyClass = 'shadow-theme-xs h-9 w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white/90 dark:placeholder:text-white/30';
    $selectClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-9 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 pr-9 text-sm text-gray-800 focus:ring-2 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
    $labelClass = 'mb-1 block text-xs font-medium text-gray-700 dark:text-gray-400';
@endphp

@section('content')
<div class="min-w-0 space-y-4" x-data="ventasForm()">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <div id="ventas-msn" class="hidden"></div>

    {{-- 1. Producto (búsqueda + código barras) --}}
    <x-common.component-card title="Producto" desc="Código de barras o busque en el listado." :compact="true">
        <form id="ventas-barcode-form" class="flex gap-2" @submit.prevent="submitBarcode()">
            <div class="flex-1">
                <label for="ventas-cod" class="sr-only">Código de barras</label>
                <input type="text" id="ventas-cod" name="cod" placeholder="Código de barras" autofocus
                    class="{{ $inputClass }} h-9" x-ref="ventasCodInput" />
            </div>
            <button type="submit" class="inline-flex h-9 min-w-[6rem] items-center justify-center gap-1.5 rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-70" :disabled="barcodeLoading">
                <span x-show="!barcodeLoading" class="inline-flex items-center gap-2">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    Código
                </span>
                <span x-show="barcodeLoading" x-cloak class="inline-flex items-center gap-2">
                    <svg class="size-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Agregando...
                </span>
            </button>
            <button type="button" @click="abrirModalProductos()" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Buscar
            </button>
        </form>
    </x-common.component-card>

    {{-- 2. Carrito --}}
    <x-common.component-card title="Carrito" desc="Detalle de productos." :compact="true">
        <div id="ventas-carrito-wrapper" class="relative min-h-[80px]">
            <div id="ventas-carrito-loading" class="absolute inset-0 z-10 hidden items-center justify-center rounded-b-2xl bg-white/80 dark:bg-gray-900/80" aria-hidden="true">
                <div class="flex flex-col items-center gap-2">
                    <svg class="size-10 animate-spin text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400" x-text="carritoLoadingText">Actualizando carrito...</span>
                </div>
            </div>
            <div id="ventas-carrito-container">
                @include('pages.ventas._carrito', ['items' => $items ?? [], 'simboloMoneda' => $simboloMoneda])
            </div>
            <p x-show="carritoFeedback" x-cloak x-transition class="mt-2 text-sm font-medium text-brand-600 dark:text-brand-400" x-text="carritoFeedback"></p>
        </div>
    </x-common.component-card>

    {{-- 3. Comprobante y cliente --}}
    <form id="frmVenta" action="{{ route('ventas.store') }}" method="post" class="space-y-3" @submit="loading = true; registrarVenta($event)">
        @csrf
        <x-common.component-card title="Comprobante y cliente" desc="Comprobante, documento y pagos." :compact="true">
            <div class="grid gap-3 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                <div>
                    <label for="tico" class="{{ $labelClass }}">Tipo comprobante <span class="text-red-500">*</span></label>
                    <x-form.select-wrapper id="tico" name="tico" required>
                        <option value="">Seleccione</option>
                        <option value="00">TICKET</option>
                        <option value="01">FACTURA</option>
                        <option value="03">BOLETA</option>
                    </x-form.select-wrapper>
                </div>
                <div>
                    <label for="serie" class="{{ $labelClass }}">Serie</label>
                    <input type="text" id="serie" name="serie" readonly class="{{ $inputReadonlyClass }}" />
                </div>
                <div>
                    <label for="correl" class="{{ $labelClass }}">Correlativo</label>
                    <input type="text" id="correl" name="correl" readonly class="{{ $inputReadonlyClass }}" />
                </div>
                <div>
                    <label for="fecha" class="{{ $labelClass }}">Fecha de emisión <span class="text-red-500">*</span></label>
                    <input type="date" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required class="{{ $inputClass }}" />
                </div>
                <input type="hidden" id="forma" name="forma" value="EFECTIVO" />
                <div>
                    <label for="td" class="{{ $labelClass }}">Tipo documento <span class="text-red-500">*</span></label>
                    <x-form.select-wrapper id="td" name="td" required>
                        <option value="1">SIN DOCUMENTO</option>
                        <option value="2" selected>DNI</option>
                        <option value="3">CARNET EXTRANJERÍA</option>
                        <option value="4">RUC</option>
                        <option value="5">PASAPORTE</option>
                        <option value="6">Ced. Diplomática</option>
                    </x-form.select-wrapper>
                </div>
                <div>
                    <label for="numero" class="{{ $labelClass }}">Número documento</label>
                    <input type="text" id="numero" name="numero" placeholder="Número de documento" maxlength="15" class="{{ $inputClass }}" />
                </div>
                <div class="sm:col-span-2 xl:col-span-3">
                    <label for="rz" class="{{ $labelClass }}">Cliente / Razón social <span class="text-red-500">*</span></label>
                    <textarea id="rz" name="rz" rows="1" required class="{{ $inputClass }} resize-none">público en general</textarea>
                </div>
                <div class="sm:col-span-2 xl:col-span-3">
                    <label for="dir" class="{{ $labelClass }}">Dirección</label>
                    <textarea id="dir" name="dir" rows="1" class="{{ $inputClass }} resize-none" placeholder="Dirección"></textarea>
                </div>
                <div class="col-span-full">
                    <label class="{{ $labelClass }}">Pagos <span class="text-red-500">*</span></label>
                    <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Suma de pagos = total a pagar.</p>
                    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="w-full min-w-[640px] text-sm" id="ventas-pagos-tabla">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                                    <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Tipo</th>
                                    <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 w-24">Monto</th>
                                    <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 w-24">Recibo</th>
                                    <th class="px-2 py-1.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 w-20">Vuelto</th>
                                    <th class="px-2 py-1.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Nº op.</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="ventas-pagos-tbody">
                                <tr class="ventas-pago-row border-b border-gray-100 dark:border-gray-800" data-index="0">
                                    <td class="px-2 py-1.5">
                                        <div class="ventas-pago-tipo-wrap max-w-[180px]">
                                            <x-form.select-wrapper name="pagos[0][tipo_pago]" id="pago-tipo-0" class="ventas-pago-tipo py-1.5 h-9 w-full text-sm" data-index="0">
                                                <option value="EFECTIVO">EFECTIVO</option>
                                                <option value="YAPE">YAPE</option>
                                                <option value="PLIN">PLIN</option>
                                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                                <option value="TARJETA">TARJETA</option>
                                                <option value="DEPOSITO EN CUENTA">DEPÓSITO EN CUENTA</option>
                                                <option value="OTRO">OTRO</option>
                                            </x-form.select-wrapper>
                                        </div>
                                    </td>
                                    <td class="px-2 py-1.5"><input type="number" name="pagos[0][monto]" min="0" step="0.01" placeholder="0" class="ventas-pago-monto {{ $inputClass }} h-9 text-right w-full" data-index="0" /></td>
                                    <td class="px-2 py-1.5 ventas-recibo-cell"><input type="number" name="pagos[0][recibo]" min="0" step="0.01" placeholder="—" class="ventas-pago-recibo {{ $inputClass }} h-9 text-right w-full" data-index="0" /></td>
                                    <td class="px-2 py-1.5 ventas-vuelto-cell"><input type="text" readonly class="ventas-pago-vuelto {{ $inputReadonlyClass }} h-9 text-right w-full text-sm" value="0.00" /></td>
                                    <td class="px-2 py-1.5 ventas-numope-cell"><input type="text" name="pagos[0][numope]" placeholder="—" class="ventas-pago-numope {{ $inputClass }} h-9 w-full" data-index="0" /></td>
                                    <td class="px-2 py-1.5"><button type="button" class="ventas-pago-quitar rounded border border-red-200 bg-red-50 px-1.5 py-1 text-xs font-medium text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400" data-index="0" title="Quitar">✕</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="flex flex-wrap items-center gap-2 border-t border-gray-200 bg-gray-50 px-2 py-1.5 dark:border-gray-700 dark:bg-gray-800/50">
                            <button type="button" id="ventas-pago-agregar" class="inline-flex h-8 items-center gap-1 rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Agregar pago
                            </button>
                            <span class="text-xs text-gray-600 dark:text-gray-400">Total: <strong id="ventas-total-pagar">{{ $simboloMoneda ?? 'S/' }} {{ number_format($totales['total'] ?? 0, 2) }}</strong></span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">Suma: <strong id="ventas-suma-pagos">0.00</strong></span>
                            <span id="ventas-pagos-diferencia" class="text-xs font-medium text-amber-600 dark:text-amber-400">Falta</span>
                        </div>
                    </div>
                    <input type="hidden" id="recibo" name="recibo" value="" />
                    <input type="hidden" id="vuelto" name="vuelto" value="" />
                </div>
            </div>
        </x-common.component-card>
    </form>

    {{-- Resumen de venta --}}
    <x-common.component-card title="Resumen" :compact="true">
        <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <div id="ventas-total-container" class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
                @include('pages.ventas._total', ['totales' => $totales ?? ['total' => 0, 'simbolo_moneda' => $simboloMoneda ?? 'S/']])
            </div>
            <div id="ventas-igv-container" class="sm:col-span-2 lg:col-span-3">
                @include('pages.ventas._igv', ['totales' => $totales ?? ['op_gravadas' => 0, 'op_exoneradas' => 0, 'op_inafectas' => 0, 'igv' => 0, 'total' => 0, 'simbolo_moneda' => $simboloMoneda ?? 'S/']])
            </div>
        </div>
    </x-common.component-card>

    {{-- Botones --}}
    <div class="flex flex-wrap justify-end gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
        <form action="{{ route('ventas.limpiar') }}" method="post" class="form-ajax-submit inline">
            @csrf
            <button type="submit" class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Nuevo
            </button>
        </form>
        <button type="submit" form="frmVenta" class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50" :disabled="loading">
            <span x-show="!loading" class="inline-flex items-center gap-2">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                Registrar venta
            </span>
            <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
                <svg class="animate-spin size-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Registrando...
            </span>
        </button>
    </div>

    {{-- Modal buscar productos (UI TailAdmin, z-index alto para quedar sobre el sidebar) --}}
    <div x-show="modalProductosOpen" x-cloak
        class="modal fixed inset-0 z-[99999] flex items-center justify-center overflow-y-auto p-5"
        @keydown.escape.window="modalProductosOpen = false"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div @click="modalProductosOpen = false" class="fixed inset-0 h-full w-full bg-black/25 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div @click.stop class="relative w-full max-w-4xl max-h-[90vh] flex flex-col rounded-3xl bg-white dark:bg-gray-900 shadow-xl"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Buscar productos</h3>
                <button type="button" @click="modalProductosOpen = false" class="flex h-11 w-11 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L11.9987 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor" /></svg>
                </button>
            </div>
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                <label for="ventas-modal-buscar" class="{{ $labelClass }}">Buscar producto</label>
                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Los resultados se actualizan al escribir (debounce 400 ms) o al pulsar Buscar.</p>
                <form id="ventas-modal-buscar-form" class="flex gap-2" @submit.prevent="ejecutarBuscarModal()">
                    <input type="search" name="buscar" id="ventas-modal-buscar" autocomplete="off"
                        placeholder="Ej.: nombre del producto, código de barras o presentación"
                        class="{{ $inputClass }} flex-1"
                        @input="debounceBuscarModal()" />
                    <button type="submit" class="shrink-0 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Buscar</button>
                </form>
            </div>
            <div class="relative flex flex-1 flex-col min-h-0">
                <div x-show="modalBuscarLoading" x-cloak class="absolute inset-0 z-10 flex items-center justify-center bg-white/90 dark:bg-gray-900/90" aria-hidden="true">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="size-10 animate-spin text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Buscando...</span>
                    </div>
                </div>
                <div id="ventas-modal-productos-body" class="flex min-h-0 flex-1 flex-col p-6"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', function() {
    Alpine.data('ventasForm', function() {
        return {
            loading: false,
            barcodeLoading: false,
            carritoLoadingText: 'Actualizando carrito...',
            carritoFeedback: '',
            modalProductosOpen: false,
            modalBuscarLoading: false,
            _buscarModalDebounce: null,
            baseUrl: @json(url('/')),
            routes: {
                carrito: @json(route('ventas.carrito')),
                total: @json(route('ventas.total')),
                igv: @json(route('ventas.igv')),
                barcode: @json(route('ventas.carrito.barcode')),
                agregar: @json(route('ventas.carrito.agregar')),
                actualizarCantidad: @json(route('ventas.carrito.actualizar-cantidad')),
                actualizarPrecio: @json(route('ventas.carrito.actualizar-precio')),
                quitar: @json(route('ventas.carrito.quitar')),
                correlativo: @json(route('ventas.correlativo')),
                store: @json(route('ventas.store')),
                productos: @json(route('ventas.productos')),
            },
            token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            async refreshCarrito(feedbackMessage) {
                const loadingEl = document.getElementById('ventas-carrito-loading');
                if (loadingEl) { loadingEl.classList.remove('hidden'); loadingEl.classList.add('flex'); }
                try {
                    const ax = window.axios;
                    if (!ax) throw new Error('Axios no disponible');
                    const [carritoRes, totalRes, igvRes] = await Promise.all([
                        ax.get(this.routes.carrito, { headers: { 'Accept': 'text/html' }, responseType: 'text' }),
                        ax.get(this.routes.total, { headers: { 'Accept': 'text/html' }, responseType: 'text' }),
                        ax.get(this.routes.igv, { headers: { 'Accept': 'text/html' }, responseType: 'text' }),
                    ]);
                    const c = document.getElementById('ventas-carrito-container');
                    const t = document.getElementById('ventas-total-container');
                    const i = document.getElementById('ventas-igv-container');
                    if (c) c.innerHTML = carritoRes.data;
                    if (t) t.innerHTML = totalRes.data;
                    if (i) i.innerHTML = igvRes.data;
                    if (typeof window.ventasActualizarPagosResumen === 'function') window.ventasActualizarPagosResumen();
                    this.rebindCarrito();
                    if (typeof window.ventasActualizarVuelto === 'function') window.ventasActualizarVuelto();
                    if (feedbackMessage) { this.carritoFeedback = feedbackMessage; setTimeout(function() { this.carritoFeedback = ''; }.bind(this), 2500); }
                } finally {
                    if (loadingEl) { loadingEl.classList.add('hidden'); loadingEl.classList.remove('flex'); }
                }
            },
            async submitBarcode() {
                const input = this.$refs.ventasCodInput || document.getElementById('ventas-cod');
                const cod = (input && input.value ? input.value : '').trim();
                if (!cod) return;
                this.barcodeLoading = true;
                try {
                    const res = await window.axios.post(this.routes.barcode, { cod });
                    const data = res.data;
                    if (data.success) {
                        if (input) input.value = '';
                        await this.refreshCarrito('Producto agregado');
                    }
                    if (data.message && typeof window.showToast === 'function') window.showToast(data.message, data.success ? 'success' : 'warning');
                    else if (data.message) alert(data.message);
                } finally {
                    this.barcodeLoading = false;
                }
            },
            rebindCarrito() {
                const self = this;
                document.querySelectorAll('.ventas-btn-quitar').forEach(function(btn) {
                    btn.onclick = function() { self.quitarItem(parseInt(btn.dataset.id, 10)); };
                });
                document.querySelectorAll('.ventas-cantidad-input').forEach(function(input) {
                    var debounceTimer;
                    function aplicarCantidad() {
                        const id = parseInt(input.dataset.id, 10);
                        const text = Math.max(1, parseInt(String(input.value).replace(/\D/g, ''), 10) || 1);
                        input.value = text;
                        if (id >= 1 && text >= 1) self.actualizarCantidad(id, text);
                    }
                    function onCantidadInput() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(aplicarCantidad, 500);
                    }
                    input.onchange = input.onblur = aplicarCantidad;
                    input.oninput = onCantidadInput;
                });
                document.querySelectorAll('.editable-precio').forEach(function(el) {
                    el.onblur = function() {
                        const id = parseInt(el.dataset.id, 10);
                        const raw = String(el.innerText || '').trim().replace(',', '.');
                        const text = parseFloat(raw) || 0;
                        if (id >= 1 && text >= 0) self.actualizarPrecio(id, text);
                    };
                });
            },
            async quitarItem(id) {
                const res = await window.axios.post(this.routes.quitar, { id });
                const data = res.data;
                if (data.success) await this.refreshCarrito('Producto quitado');
            },
            async actualizarCantidad(id, text) {
                const res = await window.axios.post(this.routes.actualizarCantidad, { id, text });
                const data = res.data;
                if (data.success) await this.refreshCarrito('Actualizado');
                else if (data.message && typeof window.showToast === 'function') window.showToast(data.message, 'warning');
                else if (data.message) alert(data.message);
            },
            async actualizarPrecio(id, text) {
                const res = await window.axios.post(this.routes.actualizarPrecio, { id, text });
                const data = res.data;
                if (data.success) await this.refreshCarrito('Actualizado');
            },
            abrirModalProductos() {
                this.modalProductosOpen = true;
                var self = this;
                setTimeout(function() { self.cargarModalProductos(self.routes.productos); }, 50);
            },
            debounceBuscarModal() {
                var self = this;
                if (this._buscarModalDebounce) clearTimeout(this._buscarModalDebounce);
                this._buscarModalDebounce = setTimeout(function() { self.ejecutarBuscarModal(); }, 400);
            },
            ejecutarBuscarModal() {
                if (this._buscarModalDebounce) { clearTimeout(this._buscarModalDebounce); this._buscarModalDebounce = null; }
                var buscar = (document.getElementById('ventas-modal-buscar') && document.getElementById('ventas-modal-buscar').value) ? document.getElementById('ventas-modal-buscar').value.trim() : '';
                var url = this.routes.productos + (buscar ? '?buscar=' + encodeURIComponent(buscar) : '');
                this.cargarModalProductos(url);
            },
            async cargarModalProductos(url) {
                const body = document.getElementById('ventas-modal-productos-body');
                if (!body) return;
                this.modalBuscarLoading = true;
                try {
                    const res = await window.axios.get(url, { headers: { 'Accept': 'text/html' }, responseType: 'text' });
                    body.innerHTML = res.data;
                    this.rebindModalProductos();
                } catch (e) {
                    body.innerHTML = '<p class="text-sm text-red-500">Error al cargar productos.</p>';
                } finally {
                    this.modalBuscarLoading = false;
                }
            },
            rebindModalProductos() {
                const self = this;
                document.querySelectorAll('.ventas-modal-btn-agregar').forEach(function(btn) {
                    if (btn.disabled) return;
                    var originalHtml = btn.innerHTML;
                    btn.onclick = async function() {
                        const row = btn.closest('tr');
                        const cantInput = row ? row.querySelector('.ventas-modal-cantidad') : null;
                        const cantidad = cantInput ? Math.max(1, Math.min(parseInt(cantInput.value, 10) || 1, parseInt(cantInput.dataset.max, 10) || 999)) : 1;
                        const id = parseInt(btn.dataset.idproducto, 10);
                        const des = btn.dataset.des || '';
                        const pres = btn.dataset.pres || '';
                        const pre = parseFloat(btn.dataset.pre || '0');
                        btn.disabled = true;
                        btn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Agregando...</span>';
                        await self.agregarProducto(id, des, pres, pre, cantidad);
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    };
                });
                document.querySelectorAll('.ventas-modal-pagina').forEach(function(a) {
                    a.onclick = function(e) { e.preventDefault(); self.cargarModalProductos(a.href); };
                });
            },
            async agregarProducto(idproducto, des, pres, pre, cantidad) {
                cantidad = cantidad || 1;
                const res = await window.axios.post(this.routes.agregar, { idproducto, des, pres, pre, cantidad });
                const data = res.data;
                if (data.success) { await this.refreshCarrito('Producto agregado'); document.getElementById('ventas-cod').value = ''; }
            },
            async registrarVenta(e) {
                e.preventDefault();
                const form = document.getElementById('frmVenta');
                const totalEl = document.getElementById('ventas-total-input');
                const totalVenta = parseFloat(totalEl && totalEl.value ? totalEl.value.replace(',', '.') : 0) || 0;
                const suma = typeof window.ventasSumaPagos === 'function' ? window.ventasSumaPagos() : 0;
                if (Math.abs(suma - totalVenta) > 0.02) {
                    if (typeof window.showToast === 'function') window.showToast('La suma de los pagos (' + suma.toFixed(2) + ') debe coincidir con el total a pagar (' + totalVenta.toFixed(2) + ').', 'error');
                    else alert('La suma de los pagos (' + suma.toFixed(2) + ') debe coincidir con el total a pagar (' + totalVenta.toFixed(2) + ').');
                    this.loading = false;
                    return;
                }
                const fd = new FormData(form);
                const td = fd.get('td');
                const numero = (fd.get('numero') || '').toString().trim();
                const rz = (fd.get('rz') || '').toString().trim().toLowerCase();
                const dir = (fd.get('dir') || '').toString().trim();
                if (td === '4') {
                    if (numero.length !== 11 || !/^\d+$/.test(numero)) {
                        if (typeof window.showToast === 'function') window.showToast('El RUC debe tener exactamente 11 dígitos numéricos.', 'error');
                        else alert('El RUC debe tener exactamente 11 dígitos numéricos.');
                        this.loading = false; return;
                    }
                    if (rz === 'público en general' || rz === 'publico en general') {
                        if (typeof window.showToast === 'function') window.showToast('No se puede registrar cliente "Público en general" para RUC.', 'error');
                        else alert('No se puede registrar cliente "Público en general" para RUC.');
                        this.loading = false; return;
                    }
                    if (dir === '') {
                        if (typeof window.showToast === 'function') window.showToast('La dirección no puede estar vacía para RUC.', 'error');
                        else alert('La dirección no puede estar vacía para RUC.');
                        this.loading = false; return;
                    }
                }
                if (td === '1') { form.querySelector('#td').value = '2'; fd.set('td', '2'); }
                if (numero === '') fd.set('numero', '00000000');
                const firstTipo = document.querySelector('.ventas-pago-tipo');
                if (firstTipo && form.querySelector('#forma')) form.querySelector('#forma').value = firstTipo.value;
                const res = await window.axios.post(this.routes.store, fd, { headers: { 'Accept': 'application/json' } });
                const data = res.data;
                this.loading = false;
                if (data.success) {
                    await this.refreshCarrito();
                    if (typeof window.ventasResetPagos === 'function') window.ventasResetPagos();
                    if (typeof window.ventasResetForm === 'function') window.ventasResetForm();
                    if (data.idventa) {
                        window.open(this.baseUrl + '/reportes/ticket?idventa=' + data.idventa + '&formato=ticket', '_blank', 'noopener');
                        if (typeof window.showToast === 'function') window.showToast(data.message || 'Venta registrada. Se abrió el ticket para imprimir.', 'success');
                    } else if (typeof window.showToast === 'function') window.showToast(data.message || 'Venta registrada.', 'success');
                } else {
                    if (typeof window.showToast === 'function') window.showToast(data.message || 'Error al registrar la venta.', 'error');
                    else alert(data.message || 'Error al registrar la venta.');
                }
            },
        };
    });
});

(function() {
    const tico = document.getElementById('tico');
    if (tico) {
        tico.addEventListener('change', function() {
            const v = tico.value;
            const serie = document.getElementById('serie');
            const correl = document.getElementById('correl');
            if (v === '01') serie.value = 'F001'; else if (v === '00') serie.value = 'T001'; else if (v === '03') serie.value = 'B001'; else { serie.value = ''; correl.value = ''; }
            if (v) {
                window.axios.post('{{ route("ventas.correlativo") }}', { tico: v }).then(function(r) { if (r.data && r.data.correlativo) correl.value = r.data.correlativo; });
            }
            const opts = document.querySelectorAll('#td option');
            if (v === '01') { opts.forEach(o => { o.disabled = (o.value !== '4'); }); }
            else if (v === '03') { opts.forEach(o => { o.disabled = (o.value === '4'); }); }
            else { opts.forEach(o => { o.disabled = false; }); }
        });
    }
    var ventasPagoIndex = 1;
    var simboloMoneda = '{{ $simboloMoneda ?? "S/" }}';
    function ventasPagosTipoOptions() {
        return '<option value="EFECTIVO">EFECTIVO</option><option value="YAPE">YAPE</option><option value="PLIN">PLIN</option><option value="TRANSFERENCIA">TRANSFERENCIA</option><option value="TARJETA">TARJETA</option><option value="DEPOSITO EN CUENTA">DEPÓSITO EN CUENTA</option><option value="OTRO">OTRO</option>';
    }
    function ventasToggleRowCells(row) {
        var tipo = (row.querySelector('.ventas-pago-tipo') || {}).value;
        var reciboCell = row.querySelector('.ventas-recibo-cell');
        var vueltoCell = row.querySelector('.ventas-vuelto-cell');
        var numopeCell = row.querySelector('.ventas-numope-cell');
        var isEfectivo = tipo === 'EFECTIVO';
        if (reciboCell) reciboCell.style.display = isEfectivo ? '' : 'none';
        if (vueltoCell) vueltoCell.style.display = isEfectivo ? '' : 'none';
        if (numopeCell) numopeCell.style.display = !isEfectivo ? '' : 'none';
    }
    function ventasActualizarVueltoRow(row) {
        var tipo = row.querySelector('.ventas-pago-tipo');
        if (tipo && tipo.value !== 'EFECTIVO') return;
        var monto = parseFloat((row.querySelector('.ventas-pago-monto') || {}).value) || 0;
        var recibo = parseFloat((row.querySelector('.ventas-pago-recibo') || {}).value) || 0;
        var vueltoEl = row.querySelector('.ventas-pago-vuelto');
        if (vueltoEl) vueltoEl.value = Math.max(0, recibo - monto).toFixed(2);
    }
    function ventasSumaPagos() {
        var total = 0;
        document.querySelectorAll('.ventas-pago-monto').forEach(function(inp) {
            total += parseFloat(inp.value) || 0;
        });
        return total;
    }
    function ventasActualizarPagosResumen() {
        var totalEl = document.getElementById('ventas-total-input');
        var totalVenta = totalEl ? (parseFloat(totalEl.value.replace(',', '.')) || 0) : 0;
        var suma = ventasSumaPagos();
        var diff = totalVenta - suma;
        var totalPagarEl = document.getElementById('ventas-total-pagar');
        var sumaEl = document.getElementById('ventas-suma-pagos');
        var diffEl = document.getElementById('ventas-pagos-diferencia');
        if (totalPagarEl) totalPagarEl.textContent = simboloMoneda + ' ' + totalVenta.toFixed(2);
        if (sumaEl) sumaEl.textContent = suma.toFixed(2);
        if (diffEl) {
            if (Math.abs(diff) < 0.02) { diffEl.textContent = 'Completo'; diffEl.className = 'text-sm font-medium text-green-600 dark:text-green-400'; }
            else if (diff > 0) { diffEl.textContent = 'Falta: ' + diff.toFixed(2); diffEl.className = 'text-sm font-medium text-amber-600 dark:text-amber-400'; }
            else { diffEl.textContent = 'Exceso: ' + (-diff).toFixed(2); diffEl.className = 'text-sm font-medium text-red-600 dark:text-red-400'; }
        }
    }
    window.ventasSumaPagos = ventasSumaPagos;
    window.ventasActualizarPagosResumen = ventasActualizarPagosResumen;
    window.ventasResetPagos = function() {
        var tbody = document.getElementById('ventas-pagos-tbody');
        if (!tbody) return;
        var rows = tbody.querySelectorAll('.ventas-pago-row');
        for (var i = 1; i < rows.length; i++) rows[i].remove();
        var first = tbody.querySelector('.ventas-pago-row');
        if (first) {
            first.querySelector('.ventas-pago-tipo').value = 'EFECTIVO';
            first.querySelector('.ventas-pago-monto').value = '';
            first.querySelector('.ventas-pago-recibo').value = '';
            first.querySelector('.ventas-pago-vuelto').value = '0.00';
            first.querySelector('.ventas-pago-numope').value = '';
            ventasToggleRowCells(first);
        }
        ventasActualizarPagosResumen();
    };

    window.ventasResetForm = function() {
        var tico = document.getElementById('tico');
        var serie = document.getElementById('serie');
        var correl = document.getElementById('correl');
        var fecha = document.getElementById('fecha');
        var forma = document.getElementById('forma');
        var td = document.getElementById('td');
        var numero = document.getElementById('numero');
        var rz = document.getElementById('rz');
        var dir = document.getElementById('dir');
        var codInput = document.getElementById('ventas-cod');
        if (fecha) fecha.value = new Date().toISOString().slice(0, 10);
        if (forma) forma.value = 'EFECTIVO';
        if (td) td.value = '2';
        if (numero) numero.value = '';
        if (rz) rz.value = 'público en general';
        if (dir) dir.value = '';
        if (codInput) codInput.value = '';
        if (tico) {
            tico.value = '00';
            if (serie) serie.value = 'T001';
            if (correl) correl.value = '';
            window.axios.post('{{ route("ventas.correlativo") }}', { tico: '00' }).then(function(r) {
                if (r.data && r.data.correlativo && correl) correl.value = r.data.correlativo;
            });
            var opts = document.querySelectorAll('#td option');
            if (opts.length) opts.forEach(function(o) { o.disabled = false; });
        }
    };
    document.getElementById('ventas-pago-agregar')?.addEventListener('click', function() {
        var tbody = document.getElementById('ventas-pagos-tbody');
        if (!tbody) return;
        var firstRow = tbody.querySelector('.ventas-pago-row');
        if (!firstRow) return;
        var idx = ventasPagoIndex++;
        var tr = document.createElement('tr');
        tr.className = 'ventas-pago-row border-b border-gray-100 dark:border-gray-800';
        tr.setAttribute('data-index', idx);
        tr.innerHTML = '<td class="px-2 py-1.5"><div class="relative z-20 bg-transparent max-w-[180px]"><select name="pagos[' + idx + '][tipo_pago]" class="ventas-pago-tipo {{ $selectClass }} h-9 w-full pr-9" data-index="' + idx + '">' + ventasPagosTipoOptions() + '</select><span class="pointer-events-none absolute top-1/2 right-3 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400"><svg class="stroke-current size-4" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg></span></div></td>' +
            '<td class="px-2 py-1.5"><input type="number" name="pagos[' + idx + '][monto]" min="0" step="0.01" placeholder="0" class="ventas-pago-monto {{ $inputClass }} h-9 text-right w-full" data-index="' + idx + '" /></td>' +
            '<td class="px-2 py-1.5 ventas-recibo-cell"><input type="number" name="pagos[' + idx + '][recibo]" min="0" step="0.01" placeholder="—" class="ventas-pago-recibo {{ $inputClass }} h-9 text-right w-full" data-index="' + idx + '" /></td>' +
            '<td class="px-2 py-1.5 ventas-vuelto-cell"><input type="text" readonly class="ventas-pago-vuelto {{ $inputReadonlyClass }} h-9 text-right w-full text-sm" value="0.00" /></td>' +
            '<td class="px-2 py-1.5 ventas-numope-cell" style="display:none"><input type="text" name="pagos[' + idx + '][numope]" placeholder="—" class="ventas-pago-numope {{ $inputClass }} h-9 w-full" data-index="' + idx + '" /></td>' +
            '<td class="px-2 py-1.5"><button type="button" class="ventas-pago-quitar rounded border border-red-200 bg-red-50 px-1.5 py-1 text-xs font-medium text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/30 dark:text-red-400" data-index="' + idx + '" title="Quitar">✕</button></td>';
        tbody.appendChild(tr);
        ventasToggleRowCells(tr);
        tr.querySelector('.ventas-pago-tipo').addEventListener('change', function() { ventasToggleRowCells(tr); });
        tr.querySelector('.ventas-pago-monto').addEventListener('input', function() { ventasActualizarVueltoRow(tr); ventasActualizarPagosResumen(); });
        tr.querySelector('.ventas-pago-recibo').addEventListener('input', function() { ventasActualizarVueltoRow(tr); ventasActualizarPagosResumen(); });
        tr.querySelector('.ventas-pago-quitar').addEventListener('click', function() { tr.remove(); ventasActualizarPagosResumen(); });
    });
    document.getElementById('ventas-pagos-tbody')?.querySelectorAll('.ventas-pago-row').forEach(function(row) {
        ventasToggleRowCells(row);
        row.querySelector('.ventas-pago-tipo')?.addEventListener('change', function() { ventasToggleRowCells(row); });
        row.querySelector('.ventas-pago-monto')?.addEventListener('input', function() { ventasActualizarVueltoRow(row); ventasActualizarPagosResumen(); });
        row.querySelector('.ventas-pago-recibo')?.addEventListener('input', function() { ventasActualizarVueltoRow(row); ventasActualizarPagosResumen(); });
        row.querySelector('.ventas-pago-quitar')?.addEventListener('click', function() {
            if (document.querySelectorAll('.ventas-pago-row').length <= 1) return;
            row.remove();
            ventasActualizarPagosResumen();
        });
    });
    document.getElementById('ventas-total-input')?.addEventListener('change', ventasActualizarPagosResumen);
    ventasActualizarPagosResumen();
})();
</script>
@endpush
