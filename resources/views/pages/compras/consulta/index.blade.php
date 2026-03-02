@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card title="Consulta compras" desc="Listado de compras. Filtre por fechas, tipo de documento o busque por número o proveedor. Ordene por columnas.">
        <div class="space-y-4">
            <form id="form-buscar-compras" method="get" action="{{ route('compras.consulta.index') }}" class="flex flex-wrap items-end gap-3">
                <input type="hidden" id="input-sort" name="sort" value="{{ $sort }}" />
                <input type="hidden" id="input-direction" name="direction" value="{{ $direction }}" />
                <div>
                    <label for="input-buscar" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Buscar</label>
                    <input type="search" id="input-buscar" name="buscar" value="{{ $buscar }}"
                        placeholder="Nº documento, proveedor..."
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 dark:bg-dark-900 h-11 w-48 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 sm:w-56" />
                </div>
                <div>
                    <label for="input-fecha-desde" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Desde</label>
                    <input type="date" id="input-fecha-desde" name="fecha_desde" value="{{ $fechaDesde }}"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 dark:bg-dark-900 h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                    <label for="input-fecha-hasta" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Hasta</label>
                    <input type="date" id="input-fecha-hasta" name="fecha_hasta" value="{{ $fechaHasta }}"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 dark:bg-dark-900 h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                <div>
                    <label for="input-docu" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo doc.</label>
                    <select id="input-docu" name="docu" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 dark:bg-dark-900 h-11 min-w-[120px] rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Todos</option>
                        <option value="FACTURA" {{ $docu === 'FACTURA' ? 'selected' : '' }}>FACTURA</option>
                        <option value="BOLETA" {{ $docu === 'BOLETA' ? 'selected' : '' }}>BOLETA</option>
                    </select>
                </div>
                <x-ui.button type="submit" variant="primary" size="md" class="shrink-0">Filtrar</x-ui.button>
            </form>

            <x-ui.content-loading
                wrapperId="compras-tabla-wrapper"
                loadingId="compras-tabla-loading"
                contentContainerId="compras-tabla-container"
            >
                @include('pages.compras.consulta._tabla-compras')
            </x-ui.content-loading>
        </div>
    </x-common.component-card>
</div>

@push('scripts')
<script>
(function() {
    var baseUrl = @json(route('compras.consulta.index'));
    var wrapper = document.getElementById('compras-tabla-wrapper');
    var container = document.getElementById('compras-tabla-container');
    var loadingEl = document.getElementById('compras-tabla-loading');
    var form = document.getElementById('form-buscar-compras');

    function setLoading(show) {
        if (!loadingEl) return;
        if (show) { loadingEl.classList.remove('hidden'); loadingEl.classList.add('flex'); }
        else { loadingEl.classList.add('hidden'); loadingEl.classList.remove('flex'); }
    }

    function buildUrl() {
        var fd = new FormData(form);
        var q = new URLSearchParams(fd);
        return baseUrl + (q.toString() ? '?' + q.toString() : '');
    }

    function updateTabla(url, pushState) {
        setLoading(true);
        window.axios.get(url, { headers: { 'Accept': 'text/html' }, responseType: 'text' })
            .then(function(r) { return r.data; })
            .then(function(html) {
                if (container) container.innerHTML = html;
                if (pushState !== false) window.history.pushState({}, '', url);
            })
            .catch(function() { window.location = url; })
            .finally(function() { setLoading(false); });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        updateTabla(buildUrl());
    });

    wrapper.addEventListener('click', function(e) {
        var a = e.target.closest('a[href*="compras/consulta"]');
        if (!a) return;
        var href = a.getAttribute('href');
        if (!href) return;
        var path = href.split('?')[0];
        if (path.match(/\/compras\/consulta\/\d+$/)) return;
        e.preventDefault();
        updateTabla(href);
    });
})();
</script>
@endpush
@endsection
