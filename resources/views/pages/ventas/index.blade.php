@extends('layouts.app')

@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $inputReadonlyClass = 'shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white/90 dark:placeholder:text-white/30';
    $selectClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
@endphp

@section('content')
<div class="min-w-0 space-y-6" x-data="ventasForm()">
    <x-common.page-breadcrumb :pageTitle="$title" />

    @if (session('success'))
        <x-ui.alert variant="success" :message="session('success')" />
    @endif
    @if (session('error'))
        <x-ui.alert variant="error" :message="session('error')" />
    @endif

    <div id="ventas-msn" class="hidden"></div>

    {{-- 1. Producto (búsqueda + código barras) --}}
    <x-common.component-card title="Producto" desc="Código de barras o busque en el listado.">
        <form id="ventas-barcode-form" class="flex gap-2" @submit.prevent="submitBarcode()">
            <div class="flex-1">
                <label for="ventas-cod" class="sr-only">Código de barras</label>
                <input type="text" id="ventas-cod" name="cod" placeholder="Código de barras" autofocus
                    class="{{ $inputClass }}" x-ref="ventasCodInput" />
            </div>
            <button type="submit" class="inline-flex h-11 min-w-[7rem] items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-70" :disabled="barcodeLoading">
                <span x-show="!barcodeLoading" class="inline-flex items-center gap-2">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                    Código
                </span>
                <span x-show="barcodeLoading" x-cloak class="inline-flex items-center gap-2">
                    <svg class="size-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Agregando...
                </span>
            </button>
            <button type="button" @click="abrirModalProductos()" class="inline-flex h-11 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                Buscar
            </button>
        </form>
    </x-common.component-card>

    {{-- 2. Carrito --}}
    <x-common.component-card title="Carrito" desc="Detalle de productos. Edite cantidad o precio en la tabla si lo necesita.">
        <div id="ventas-carrito-wrapper" class="relative min-h-[120px]">
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

    {{-- 3. Comprobante y cliente (ancho completo, debajo del carrito) --}}
    <form id="frmVenta" action="{{ route('ventas.store') }}" method="post" class="space-y-4" @submit="loading = true; registrarVenta($event)">
        @csrf
        <x-common.component-card title="Comprobante y cliente" desc="Complete tipo de comprobante, forma de pago y datos del cliente.">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
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
                <div>
                    <label for="forma" class="{{ $labelClass }}">Forma de pago <span class="text-red-500">*</span></label>
                    <x-form.select-wrapper id="forma" name="forma" required>
                        <option value="">Seleccione</option>
                        <option value="EFECTIVO">EFECTIVO</option>
                        <option value="YAPE">YAPE</option>
                        <option value="PLIN">PLIN</option>
                        <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                        <option value="TARJETA">TARJETA</option>
                        <option value="DEPOSITO EN CUENTA">DEPÓSITO EN CUENTA</option>
                        <option value="OTRO">OTRO</option>
                    </x-form.select-wrapper>
                </div>
                <div id="ventas-numope-wrap" class="hidden">
                    <label for="numope" class="{{ $labelClass }}">Nº operación / Referencia</label>
                    <input type="text" id="numope" name="numope" placeholder="Opcional" class="{{ $inputClass }}" />
                </div>
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
                <div class="sm:col-span-2">
                    <label for="rz" class="{{ $labelClass }}">Cliente / Razón social <span class="text-red-500">*</span></label>
                    <textarea id="rz" name="rz" rows="2" required class="{{ $inputClass }}">público en general</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label for="dir" class="{{ $labelClass }}">Dirección</label>
                    <textarea id="dir" name="dir" rows="2" class="{{ $inputClass }}" placeholder="Dirección del cliente"></textarea>
                </div>
                <div id="ventas-efectivo-wrap" class="space-y-2 hidden sm:col-span-2">
                    <div class="grid gap-2 sm:grid-cols-2">
                        <div>
                            <label for="recibo" class="{{ $labelClass }}">Efectivo recibido</label>
                            <div class="flex gap-2">
                                <input type="number" id="recibo" name="recibo" min="0" step="0.01" placeholder="0.00" class="{{ $inputClass }}" />
                                <button type="button" id="ventas-btn-calcular-vuelto" class="shrink-0 rounded-lg bg-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Calcular</button>
                            </div>
                        </div>
                        <div>
                            <label for="vuelto" class="{{ $labelClass }}">Vuelto</label>
                            <input type="text" id="vuelto" name="vuelto" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                    </div>
                </div>
            </div>
        </x-common.component-card>
    </form>

    {{-- Resumen de venta a ancho completo --}}
    <x-common.component-card title="Resumen de venta" desc="Totales según los productos en el carrito.">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <div id="ventas-total-container" class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                @include('pages.ventas._total', ['totales' => $totales ?? ['total' => 0, 'simbolo_moneda' => $simboloMoneda ?? 'S/']])
            </div>
            <div id="ventas-igv-container" class="sm:col-span-2 lg:col-span-3">
                @include('pages.ventas._igv', ['totales' => $totales ?? ['op_gravadas' => 0, 'op_exoneradas' => 0, 'op_inafectas' => 0, 'igv' => 0, 'total' => 0, 'simbolo_moneda' => $simboloMoneda ?? 'S/']])
            </div>
        </div>
    </x-common.component-card>

    {{-- Botones al final del formulario --}}
    <div class="flex flex-wrap justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
        <a href="{{ route('ventas.limpiar') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            Nuevo
        </a>
        <button type="submit" form="frmVenta" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50" :disabled="loading">
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
                    const [carritoRes, totalRes, igvRes] = await Promise.all([
                        fetch(this.routes.carrito, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } }),
                        fetch(this.routes.total, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } }),
                        fetch(this.routes.igv, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } }),
                    ]);
                    const c = document.getElementById('ventas-carrito-container');
                    const t = document.getElementById('ventas-total-container');
                    const i = document.getElementById('ventas-igv-container');
                    if (c) c.innerHTML = await carritoRes.text();
                    if (t) t.innerHTML = await totalRes.text();
                    if (i) i.innerHTML = await igvRes.text();
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
                    const res = await fetch(this.routes.barcode, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.token, 'Accept': 'application/json' }, body: JSON.stringify({ cod }) });
                    const data = await res.json();
                    if (data.success) {
                        if (input) input.value = '';
                        await this.refreshCarrito('Producto agregado');
                    }
                    if (data.message) alert(data.message);
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
                    input.onchange = input.onblur = function() {
                        const id = parseInt(input.dataset.id, 10);
                        const text = Math.max(1, parseInt(String(input.value).replace(/\D/g, ''), 10) || 1);
                        input.value = text;
                        if (text >= 1) self.actualizarCantidad(id, text);
                    };
                });
                document.querySelectorAll('.editable-precio').forEach(function(el) {
                    el.onblur = function() {
                        const id = parseInt(el.dataset.id, 10);
                        const text = parseFloat(String(el.innerText).replace(',', '.')) || 0;
                        if (text >= 0) self.actualizarPrecio(id, text);
                    };
                });
            },
            async quitarItem(id) {
                const res = await fetch(this.routes.quitar, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.token, 'Accept': 'application/json' }, body: JSON.stringify({ id }) });
                const data = await res.json();
                if (data.success) await this.refreshCarrito('Producto quitado');
            },
            async actualizarCantidad(id, text) {
                const res = await fetch(this.routes.actualizarCantidad, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.token, 'Accept': 'application/json' }, body: JSON.stringify({ id, text }) });
                const data = await res.json();
                if (data.success) await this.refreshCarrito('Actualizado');
                else if (data.message) alert(data.message);
            },
            async actualizarPrecio(id, text) {
                const res = await fetch(this.routes.actualizarPrecio, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.token, 'Accept': 'application/json' }, body: JSON.stringify({ id, text }) });
                const data = await res.json();
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
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } });
                    body.innerHTML = await res.text();
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
                const res = await fetch(this.routes.agregar, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.token, 'Accept': 'application/json' }, body: JSON.stringify({ idproducto, des, pres, pre, cantidad }) });
                const data = await res.json();
                if (data.success) { await this.refreshCarrito('Producto agregado'); document.getElementById('ventas-cod').value = ''; }
            },
            async registrarVenta(e) {
                e.preventDefault();
                const form = document.getElementById('frmVenta');
                const fd = new FormData(form);
                const td = fd.get('td');
                const numero = (fd.get('numero') || '').toString().trim();
                const rz = (fd.get('rz') || '').toString().trim().toLowerCase();
                const dir = (fd.get('dir') || '').toString().trim();
                if (td === '4') {
                    if (numero.length !== 11 || !/^\d+$/.test(numero)) { alert('El RUC debe tener exactamente 11 dígitos numéricos.'); this.loading = false; return; }
                    if (rz === 'público en general' || rz === 'publico en general') { alert('No se puede registrar cliente "Público en general" para RUC.'); this.loading = false; return; }
                    if (dir === '') { alert('La dirección no puede estar vacía para RUC.'); this.loading = false; return; }
                }
                if (td === '1') { form.querySelector('#td').value = '2'; fd.set('td', '2'); }
                if (numero === '') fd.set('numero', '00000000');
                const res = await fetch(this.routes.store, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                const data = await res.json();
                this.loading = false;
                if (data.success) {
                    await this.refreshCarrito();
                    document.getElementById('recibo').value = ''; document.getElementById('vuelto').value = '';
                    if (data.idventa) window.open(this.baseUrl + '/reportes/ticket?idventa=' + data.idventa, '_blank');
                    alert(data.message || 'Venta registrada.');
                } else {
                    alert(data.message || 'Error al registrar la venta.');
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
                fetch('{{ route("ventas.correlativo") }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '', 'Accept': 'application/json' }, body: JSON.stringify({ tico: v }) })
                    .then(r => r.json()).then(d => { if (d.correlativo) correl.value = d.correlativo; });
            }
            const opts = document.querySelectorAll('#td option');
            if (v === '01') { opts.forEach(o => { o.disabled = (o.value !== '4'); }); }
            else if (v === '03') { opts.forEach(o => { o.disabled = (o.value === '4'); }); }
            else { opts.forEach(o => { o.disabled = false; }); }
        });
    }
    const forma = document.getElementById('forma');
    if (forma) {
        forma.addEventListener('change', function() {
            const f = forma.value;
            document.getElementById('ventas-efectivo-wrap').classList.toggle('hidden', f !== 'EFECTIVO');
            document.getElementById('ventas-numope-wrap').classList.toggle('hidden', f !== 'TARJETA' && f !== 'YAPE' && f !== 'PLIN' && f !== 'TRANSFERENCIA' && f !== 'OTRO');
            if (f === 'EFECTIVO' && typeof window.ventasActualizarVuelto === 'function') window.ventasActualizarVuelto();
        });
    }
    function actualizarVuelto() {
        var forma = document.getElementById('forma');
        if (forma && forma.value !== 'EFECTIVO') return;
        var reciboEl = document.getElementById('recibo');
        var totalEl = document.getElementById('ventas-total-input');
        var vueltoEl = document.getElementById('vuelto');
        if (!reciboEl || !totalEl || !vueltoEl) return;
        var recibo = parseFloat(reciboEl.value) || 0;
        var total = parseFloat(totalEl.value) || 0;
        vueltoEl.value = (recibo - total).toFixed(2);
    }
    window.ventasActualizarVuelto = actualizarVuelto;
    document.getElementById('recibo')?.addEventListener('input', actualizarVuelto);
    document.getElementById('recibo')?.addEventListener('change', actualizarVuelto);
    document.getElementById('ventas-btn-calcular-vuelto')?.addEventListener('click', actualizarVuelto);
})();
</script>
@endpush
