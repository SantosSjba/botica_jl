@extends('layouts.app')

@section('content')
@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $inputReadonlyClass = 'shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white/90 dark:placeholder:text-white/30';
    $selectClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
    $errorClass = ' border-red-500 dark:border-red-500';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card title="Registrar compra" desc="Busque productos, agregue al carrito y complete los datos del documento. Use Nuevo para vaciar el carrito.">
        <form action="{{ route('compras.store') }}" method="post" class="form-ajax-submit" x-data="{ loading: false }" @submit="loading = true" id="form-compras">
            @csrf

            {{-- Datos del documento (al inicio) --}}
            <div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-white/[0.03]">
                <h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">Datos del documento</h4>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="docu" class="{{ $labelClass }}">Tipo documento <span class="text-red-500">*</span></label>
                        <div x-data="{ isOptionSelected: true }" class="relative z-20 bg-transparent">
                            <select id="docu" name="docu" required class="{{ $selectClass }} @error('docu'){{ $errorClass }}@enderror">
                                @foreach($tiposDoc as $t)
                                    <option value="{{ $t }}" {{ old('docu', 'FACTURA') === $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            </span>
                        </div>
                    </div>
                    <div>
                        <label for="num_docu" class="{{ $labelClass }}">Nº de orden / documento <span class="text-red-500">*</span></label>
                        <input type="text" id="num_docu" name="num_docu" value="{{ old('num_docu') }}" required maxlength="50"
                            placeholder="Número del documento"
                            class="{{ $inputClass }} @error('num_docu'){{ $errorClass }}@enderror" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $labelClass }}">Proveedor <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" id="compras-buscar-proveedor" autocomplete="off"
                                placeholder="Buscar proveedor (mín. 2 caracteres)..."
                                class="{{ $inputClass }} @error('idcliente'){{ $errorClass }}@enderror" />
                            <input type="hidden" name="idcliente" id="compras-idcliente" value="{{ old('idcliente') }}" required />
                            <div id="compras-proveedores-list" class="absolute top-full left-0 z-30 mt-1 hidden max-h-60 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"></div>
                        </div>
                        @error('idcliente')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="fecha" class="{{ $labelClass }}">Fecha de emisión</label>
                        <input type="date" id="fecha" name="fecha" value="{{ old('fecha', $fecha) }}" required readonly
                            class="{{ $inputReadonlyClass }}" />
                    </div>
                </div>
            </div>

            {{-- Búsqueda de producto --}}
            <div class="mb-6">
                <label class="{{ $labelClass }}">Producto</label>
                <div class="relative flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" id="compras-buscar-producto" autocomplete="off"
                            placeholder="Buscar por descripción o código (mín. 2 caracteres)..."
                            class="{{ $inputClass }}" />
                        <input type="hidden" id="compras-idproducto" />
                        <div id="compras-productos-list" class="absolute top-full left-0 z-30 mt-1 hidden max-h-60 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"></div>
                    </div>
                    <button type="button" id="compras-btn-agregar" class="inline-flex h-11 shrink-0 items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50" disabled>
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Agregar
                    </button>
                </div>
            </div>

            {{-- Tabla del carrito --}}
            <div class="mb-6" id="compras-carrito-tabla-container">
                @include('pages.compras._carrito-tabla', ['items' => $items])
            </div>

            {{-- Resumen a ancho completo --}}
            <div id="compras-carrito-totales-container" class="mb-4 w-full">
                @include('pages.compras._carrito-totales', [
                    'subtotal' => $subtotal,
                    'igv' => $igv,
                    'total' => $total,
                    'simboloMoneda' => $simboloMoneda,
                ])
            </div>

            {{-- Botones debajo del resumen --}}
            <div class="mb-6 flex flex-wrap gap-3">
                <form action="{{ route('compras.limpiar') }}" method="post" class="form-ajax-submit inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Nuevo (vaciar carrito)
                    </button>
                </form>
                <x-ui.button-loader type="submit" label="Registrar compra" loading-text="Guardando..." class="!px-5 !py-3.5 bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 rounded-lg font-medium text-sm" />
            </div>
        </form>
    </x-common.component-card>
</div>

@push('scripts')
<script>
(function() {
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var baseUrl = @json(url('/compras'));
    var tablaContainer = document.getElementById('compras-carrito-tabla-container');
    var totalesContainer = document.getElementById('compras-carrito-totales-container');
    var inputProducto = document.getElementById('compras-buscar-producto');
    var listProductos = document.getElementById('compras-productos-list');
    var idProductoHidden = document.getElementById('compras-idproducto');
    var btnAgregar = document.getElementById('compras-btn-agregar');
    var inputProveedor = document.getElementById('compras-buscar-proveedor');
    var listProveedores = document.getElementById('compras-proveedores-list');
    var idClienteHidden = document.getElementById('compras-idcliente');
    var productoSeleccionado = null;
    var debounceProducto = null;
    var debounceProveedor = null;

    function actualizarCarrito() {
        if (typeof window.axios === 'undefined') return;
        window.axios.get(baseUrl + '/carrito/partials').then(function(r) {
            var data = r.data;
            if (data.table) tablaContainer.innerHTML = data.table;
            if (data.totales) totalesContainer.innerHTML = data.totales;
            bindCarritoEvents();
        }).catch(function() {});
    }

    function bindCarritoEvents() {
        tablaContainer.querySelectorAll('.compras-quitar').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = btn.getAttribute('data-idproducto');
                window.axios.post(baseUrl + '/carrito/quitar', { idproducto: id }).then(function(r) {
                    var data = r.data;
                    if (data.ok) { tablaContainer.innerHTML = data.table; totalesContainer.innerHTML = data.totales; bindCarritoEvents(); }
                    else if (data.message && typeof window.showToast === 'function') window.showToast(data.message, 'error');
                });
            });
        });
        tablaContainer.querySelectorAll('.compras-cantidad').forEach(function(inp) {
            inp.addEventListener('change', function() {
                var row = inp.closest('tr');
                var id = row.getAttribute('data-idproducto');
                var cant = parseInt(inp.value, 10) || 1;
                if (cant < 1) { inp.value = 1; cant = 1; }
                window.axios.post(baseUrl + '/carrito/actualizar-cantidad', { idproducto: id, cantidad: cant }).then(function(r) {
                    var data = r.data;
                    if (data.ok) { tablaContainer.innerHTML = data.table; totalesContainer.innerHTML = data.totales; bindCarritoEvents(); }
                    else if (data.message && typeof window.showToast === 'function') window.showToast(data.message, 'error');
                });
            });
        });
        tablaContainer.querySelectorAll('.compras-precio').forEach(function(inp) {
            inp.addEventListener('change', function() {
                var row = inp.closest('tr');
                var id = row.getAttribute('data-idproducto');
                var precio = parseFloat(inp.value) || 0;
                window.axios.post(baseUrl + '/carrito/actualizar-precio', { idproducto: id, precio: precio }).then(function(r) {
                    var data = r.data;
                    if (data.ok) { tablaContainer.innerHTML = data.table; totalesContainer.innerHTML = data.totales; bindCarritoEvents(); }
                    else if (data.message && typeof window.showToast === 'function') window.showToast(data.message, 'error');
                });
            });
        });
    }

    inputProducto.addEventListener('input', function() {
        clearTimeout(debounceProducto);
        var q = inputProducto.value.trim();
        productoSeleccionado = null;
        idProductoHidden.value = '';
        btnAgregar.disabled = true;
        if (q.length < 2) { listProductos.classList.add('hidden'); listProductos.innerHTML = ''; return; }
        debounceProducto = setTimeout(function() {
            window.axios.get(baseUrl + '/buscar-productos', { params: { q: q } })
                .then(function(r) { var arr = r.data; if (!Array.isArray(arr)) arr = []; return arr; })
                .then(function(arr) {
                    listProductos.innerHTML = '';
                    if (arr.length === 0) { listProductos.classList.add('hidden'); return; }
                    arr.forEach(function(p) {
                        var div = document.createElement('div');
                        div.className = 'cursor-pointer px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-100 dark:text-white/90 dark:hover:bg-gray-700';
                        div.textContent = p.descripcion + ' — ' + p.presentacion + ' — S/ ' + p.precio;
                        div.dataset.id = p.idproducto;
                        div.dataset.descripcion = p.descripcion;
                        div.dataset.presentacion = p.presentacion;
                        div.dataset.precio = p.precio;
                        div.addEventListener('click', function() {
                            productoSeleccionado = p;
                            inputProducto.value = p.descripcion + ' | ' + p.presentacion;
                            idProductoHidden.value = p.idproducto;
                            listProductos.classList.add('hidden');
                            listProductos.innerHTML = '';
                            btnAgregar.disabled = false;
                        });
                        listProductos.appendChild(div);
                    });
                    listProductos.classList.remove('hidden');
                });
        }, 300);
    });
    inputProducto.addEventListener('focus', function() { if (listProductos.children.length) listProductos.classList.remove('hidden'); });
    document.addEventListener('click', function(e) { if (!inputProducto.contains(e.target) && !listProductos.contains(e.target)) listProductos.classList.add('hidden'); });

    btnAgregar.addEventListener('click', function() {
        if (!productoSeleccionado) return;
        btnAgregar.disabled = true;
        window.axios.post(baseUrl + '/carrito/agregar', {
            idproducto: productoSeleccionado.idproducto,
            descripcion: productoSeleccionado.descripcion,
            presentacion: productoSeleccionado.presentacion,
            precio: productoSeleccionado.precio
        }).then(function(r) { var data = r.data; return data; }).then(function(data) {
            if (data.ok) {
                tablaContainer.innerHTML = data.table;
                totalesContainer.innerHTML = data.totales;
                bindCarritoEvents();
                inputProducto.value = '';
                idProductoHidden.value = '';
                productoSeleccionado = null;
            } else {
                if (data.message && typeof window.showToast === 'function') window.showToast(data.message, 'error');
                else if (data.message) alert(data.message);
            }
            btnAgregar.disabled = true;
        });
    });

    inputProveedor.addEventListener('input', function() {
        clearTimeout(debounceProveedor);
        var q = inputProveedor.value.trim();
        idClienteHidden.value = '';
        if (q.length < 2) { listProveedores.classList.add('hidden'); listProveedores.innerHTML = ''; return; }
        debounceProveedor = setTimeout(function() {
            window.axios.get(baseUrl + '/buscar-proveedores', { params: { q: q } })
                .then(function(r) { var arr = r.data; if (!Array.isArray(arr)) arr = []; return arr; })
                .then(function(arr) {
                    listProveedores.innerHTML = '';
                    if (arr.length === 0) { listProveedores.classList.add('hidden'); return; }
                    arr.forEach(function(c) {
                        var div = document.createElement('div');
                        div.className = 'cursor-pointer px-4 py-2.5 text-sm text-gray-800 hover:bg-gray-100 dark:text-white/90 dark:hover:bg-gray-700';
                        div.textContent = c.nombres;
                        div.dataset.id = c.idcliente;
                        div.dataset.nombres = c.nombres;
                        div.addEventListener('click', function() {
                            idClienteHidden.value = c.idcliente;
                            inputProveedor.value = c.nombres;
                            listProveedores.classList.add('hidden');
                            listProveedores.innerHTML = '';
                        });
                        listProveedores.appendChild(div);
                    });
                    listProveedores.classList.remove('hidden');
                });
        }, 300);
    });
    document.addEventListener('click', function(e) { if (!inputProveedor.contains(e.target) && !listProveedores.contains(e.target)) listProveedores.classList.add('hidden'); });

    bindCarritoEvents();
})();
</script>
@endpush
@endsection
