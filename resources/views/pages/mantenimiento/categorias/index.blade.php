@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    @if (session('success'))
        <x-ui.alert variant="success" :message="session('success')" />
    @endif
    @if (session('error'))
        <x-ui.alert variant="error" :message="session('error')" />
    @endif

    <x-common.component-card title="Forma farmacéutica" desc="Listado de formas farmacéuticas. Busque, ordene por columnas o agregue nuevos registros.">
        <div class="space-y-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <form id="form-buscar-categorias" method="get" action="{{ route('mantenimiento.categorias.index') }}" class="flex flex-wrap items-end gap-3">
                    <input type="hidden" id="input-sort" name="sort" value="{{ $sort }}" />
                    <input type="hidden" id="input-direction" name="direction" value="{{ $direction }}" />
                    <div>
                        <label for="input-buscar" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Buscar</label>
                        <input type="search" id="input-buscar" name="buscar" value="{{ $buscar }}"
                            placeholder="Forma farmacéutica, F.F. simplificada..."
                            class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 dark:bg-dark-900 h-11 w-48 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 sm:w-56" />
                    </div>
                    <x-ui.button type="submit" variant="primary" size="md" class="shrink-0">Buscar</x-ui.button>
                </form>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('mantenimiento.categorias.create') }}">
                        <x-ui.button type="button" variant="primary" size="md" class="inline-flex items-center gap-2">
                            <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Nuevo
                        </x-ui.button>
                    </a>
                    <x-ui.button type="button" id="btn-actualizar-categorias" variant="outline" size="md" class="inline-flex items-center gap-2" title="Actualizar datos">
                        <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Actualizar
                    </x-ui.button>
                </div>
            </div>

            <x-ui.content-loading
                wrapperId="categorias-tabla-wrapper"
                loadingId="categorias-tabla-loading"
                contentContainerId="categorias-tabla-container"
            >
                @include('pages.mantenimiento.categorias._tabla-categorias')
            </x-ui.content-loading>
        </div>
    </x-common.component-card>
</div>

@push('scripts')
<script>
(function() {
    var baseUrl = @json(route('mantenimiento.categorias.index'));
    var wrapper = document.getElementById('categorias-tabla-wrapper');
    var container = document.getElementById('categorias-tabla-container');
    var loadingEl = document.getElementById('categorias-tabla-loading');
    var form = document.getElementById('form-buscar-categorias');
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
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                if (container) container.innerHTML = html;
                if (pushState !== false) window.history.pushState({}, '', url);
                var u = new URL(url, window.location.origin);
                inputSort.value = u.searchParams.get('sort') || 'forma_farmaceutica';
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

    document.getElementById('btn-actualizar-categorias').addEventListener('click', function() {
        aplicarBusqueda();
    });

    wrapper.addEventListener('click', function(e) {
        var a = e.target.closest('a[href*="mantenimiento/categorias"]');
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
