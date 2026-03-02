@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card title="Consulta ventas" desc="Facturas y boletas. Filtre por rango de fechas. Ordene por columnas.">
        <div class="space-y-4">
            <form id="form-buscar-ventas" method="get" action="{{ route('ventas.consulta.index') }}" class="flex flex-wrap items-end gap-3">
                <input type="hidden" id="input-sort" name="sort" value="{{ $sort }}" />
                <input type="hidden" id="input-direction" name="direction" value="{{ $direction }}" />
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
                <x-ui.button type="submit" variant="primary" size="md" class="shrink-0">Filtrar</x-ui.button>
            </form>

            <x-ui.content-loading
                wrapperId="ventas-consulta-wrapper"
                loadingId="ventas-consulta-loading"
                contentContainerId="ventas-consulta-container"
            >
                @include('pages.ventas.consulta._tabla-ventas')
            </x-ui.content-loading>
        </div>
    </x-common.component-card>
</div>

@push('scripts')
<script>
(function() {
    var baseUrl = @json(route('ventas.consulta.index'));
    var container = document.getElementById('ventas-consulta-container');
    var loadingEl = document.getElementById('ventas-consulta-loading');
    var form = document.getElementById('form-buscar-ventas');

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

    document.getElementById('ventas-consulta-wrapper')?.addEventListener('click', function(e) {
        var a = e.target.closest('a[href*="ventas/consulta"]');
        if (!a) return;
        var href = a.getAttribute('href');
        if (!href) return;
        var path = href.split('?')[0];
        if (path.match(/\/ventas\/consulta\/\d+$/)) return;
        e.preventDefault();
        updateTabla(href);
    });
})();
</script>
@endpush
@endsection
