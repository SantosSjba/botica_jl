@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb pageTitle="Consultas - Productos farmacéuticos" />

    <div class="min-w-0 overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        {{-- Barra búsqueda y actualizar --}}
        <div class="flex flex-col gap-4 border-b border-gray-200 px-4 py-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Productos farmacéuticos</h3>
            <div class="flex flex-wrap items-center gap-3">
                <form id="form-buscar-productos" method="get" action="{{ route('consulta.productos') }}" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" id="input-sort" name="sort" value="{{ $sort }}" />
                    <input type="hidden" id="input-direction" name="direction" value="{{ $direction }}" />
                    <label for="input-buscar" class="sr-only">Buscar</label>
                    <input type="search" id="input-buscar" name="buscar" value="{{ $buscar }}"
                        placeholder="Código, descripción, presentación..."
                        class="h-10 w-48 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-56" />
                    <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-600">
                        Buscar
                    </button>
                </form>
                <button type="button" id="btn-actualizar-productos" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]" title="Actualizar datos">
                    <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    Actualizar
                </button>
            </div>
        </div>

        <x-ui.content-loading
            wrapperId="consulta-tabla-wrapper"
            loadingId="consulta-tabla-loading"
            contentContainerId="consulta-tabla-container"
        >
            @include('pages.consulta._tabla-productos')
        </x-ui.content-loading>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var baseUrl = @json(route('consulta.productos'));
    var wrapper = document.getElementById('consulta-tabla-wrapper');
    var container = document.getElementById('consulta-tabla-container');
    var loadingEl = document.getElementById('consulta-tabla-loading');
    var form = document.getElementById('form-buscar-productos');
    var inputBuscar = document.getElementById('input-buscar');
    var inputSort = document.getElementById('input-sort');
    var inputDirection = document.getElementById('input-direction');
    var debounceTimer = null;

    function setLoading(show) {
        if (!loadingEl) return;
        if (show) { loadingEl.classList.remove('hidden'); loadingEl.classList.add('flex'); }
        else { loadingEl.classList.add('hidden'); loadingEl.classList.remove('flex'); }
    }

    function buildUrl(params) {
        var q = new URLSearchParams(params);
        return baseUrl + (q.toString() ? '?' + q.toString() : '');
    }

    function updateTabla(url, pushState) {
        setLoading(true);
        window.axios.get(url, { headers: { 'Accept': 'text/html' }, responseType: 'text' })
            .then(function(r) { return r.data; })
            .then(function(html) {
                if (container) container.innerHTML = html;
                if (pushState !== false) window.history.pushState({}, '', url);
                var u = new URL(url, window.location.origin);
                inputSort.value = u.searchParams.get('sort') || 'codigo';
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

    inputBuscar.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(aplicarBusqueda, 400);
    });

    document.getElementById('btn-actualizar-productos').addEventListener('click', function() {
        aplicarBusqueda();
    });

    wrapper.addEventListener('click', function(e) {
        var a = e.target.closest('a[href*="consulta/productos"]');
        if (!a) return;
        e.preventDefault();
        updateTabla(a.getAttribute('href'));
    });
})();
</script>
@endpush
@endsection
