@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <div class="min-w-0 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-4 border-b border-gray-200 px-4 py-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Cliente / Laboratorio</h3>
            <div class="flex flex-wrap items-center gap-3">
                <form id="form-buscar-clientes" method="get" action="{{ route('mantenimiento.clientes.index') }}" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" id="input-sort" name="sort" value="{{ $sort }}" />
                    <input type="hidden" id="input-direction" name="direction" value="{{ $direction }}" />
                    <label for="input-buscar" class="sr-only">Buscar</label>
                    <input type="search" id="input-buscar" name="buscar" value="{{ $buscar }}"
                        placeholder="Razón social, dirección, documento..."
                        class="h-10 w-48 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-56" />
                    <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-600">
                        Buscar
                    </button>
                </form>
                <a href="{{ route('mantenimiento.clientes.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-brand-600">
                    <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nuevo
                </a>
                <button type="button" id="btn-actualizar-clientes" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]" title="Actualizar datos">
                    <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Actualizar
                </button>
            </div>
        </div>

        <div id="clientes-tabla-wrapper" class="relative">
            <div id="clientes-tabla-loading" class="absolute inset-0 z-10 hidden items-center justify-center rounded-b-2xl bg-white/80 dark:bg-gray-900/80" aria-hidden="true">
                <div class="flex flex-col items-center gap-2">
                    <svg class="size-10 animate-spin text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Cargando...</span>
                </div>
            </div>
            @include('pages.mantenimiento.clientes._tabla-clientes')
        </div>
    </div>
</div>

@if (session('success'))
    <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-400" role="alert">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400" role="alert">
        {{ session('error') }}
    </div>
@endif

@push('scripts')
<script>
(function() {
    var baseUrl = @json(route('mantenimiento.clientes.index'));
    var wrapper = document.getElementById('clientes-tabla-wrapper');
    var loadingEl = document.getElementById('clientes-tabla-loading');
    var form = document.getElementById('form-buscar-clientes');
    var inputBuscar = document.getElementById('input-buscar');
    var inputSort = document.getElementById('input-sort');
    var inputDirection = document.getElementById('input-direction');
    var debounceTimer = null;

    function setLoading(show) {
        if (!loadingEl) return;
        if (show) {
            loadingEl.classList.remove('hidden');
            loadingEl.classList.add('flex');
        } else {
            loadingEl.classList.add('hidden');
            loadingEl.classList.remove('flex');
        }
    }

    function buildUrl(params) {
        var q = new URLSearchParams(params);
        return baseUrl + (q.toString() ? '?' + q.toString() : '');
    }

    function updateTabla(url, pushState) {
        setLoading(true);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var container = wrapper.querySelector('.clientes-tabla-container');
                if (container) container.outerHTML = html;
                if (pushState !== false) window.history.pushState({}, '', url);
                var u = new URL(url, window.location.origin);
                inputSort.value = u.searchParams.get('sort') || 'nombres';
                inputDirection.value = u.searchParams.get('direction') || 'asc';
            })
            .catch(function() { window.location = url; })
            .finally(function() { setLoading(false); });
    }

    function aplicarBusqueda() {
        var params = { sort: inputSort.value, direction: inputDirection.value };
        if (inputBuscar.value.trim()) params.buscar = inputBuscar.value.trim();
        updateTabla(buildUrl(params));
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        aplicarBusqueda();
    });

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(aplicarBusqueda, 400);
        });
    }

    document.getElementById('btn-actualizar-clientes').addEventListener('click', function() {
        aplicarBusqueda();
    });

    wrapper.addEventListener('click', function(e) {
        var a = e.target.closest('a[href*="mantenimiento/clientes"]');
        if (!a) return;
        var href = a.getAttribute('href');
        if (href.indexOf('/edit') !== -1 || href.indexOf('/create') !== -1) return;
        if (href === baseUrl || href.indexOf(baseUrl + '?') === 0 || (href.indexOf('page=') !== -1)) {
            e.preventDefault();
            updateTabla(href);
        }
    });
})();
</script>
@endpush
@endsection
