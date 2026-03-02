@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card title="Consulta tickets" desc="Tickets (serie T001). Filtre por fechas. Puede anular un ticket y se devolverá el stock.">
        <div class="space-y-4">
            <form id="form-buscar-tickets" method="get" action="{{ route('ventas.tickets.index') }}" class="flex flex-wrap items-end gap-3">
                <input type="hidden" name="sort" value="{{ $sort }}" />
                <input type="hidden" name="direction" value="{{ $direction }}" />
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
                wrapperId="tickets-tabla-wrapper"
                loadingId="tickets-tabla-loading"
                contentContainerId="tickets-tabla-container"
            >
                @include('pages.ventas.tickets._tabla-tickets')
            </x-ui.content-loading>
        </div>
    </x-common.component-card>
</div>

@push('scripts')
<script>
(function() {
    var baseUrl = @json(route('ventas.tickets.index'));
    var anularUrl = @json(route('ventas.tickets.anular'));
    var container = document.getElementById('tickets-tabla-container');
    var loadingEl = document.getElementById('tickets-tabla-loading');
    var form = document.getElementById('form-buscar-tickets');
    var token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

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
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                if (container) container.innerHTML = html;
                if (pushState !== false) window.history.pushState({}, '', url);
                bindAnular();
            })
            .catch(function() { window.location = url; })
            .finally(function() { setLoading(false); });
    }
    function bindAnular() {
        document.querySelectorAll('.btn-anular-ticket').forEach(function(btn) {
            if (btn._bound) return;
            btn._bound = true;
            btn.addEventListener('click', function() {
                var id = parseInt(btn.dataset.id, 10);
                if (!id) return;
                if (!confirm('¿Anular este ticket? Se devolverá el stock de los productos.')) return;
                btn.disabled = true;
                fetch(anularUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (typeof window.showToast === 'function') window.showToast(data.message || (data.success ? 'Anulado.' : 'Error.'), data.success ? 'success' : 'error');
                    else alert(data.message || (data.success ? 'Anulado.' : 'Error.'));
                    if (data.success) updateTabla(buildUrl());
                })
                .finally(function() { btn.disabled = false; });
            });
        });
    }
    form.addEventListener('submit', function(e) { e.preventDefault(); updateTabla(buildUrl()); });
    document.getElementById('tickets-tabla-wrapper').addEventListener('click', function(e) {
        var a = e.target.closest('a[href*="ventas/tickets"]');
        if (!a) return;
        var href = a.getAttribute('href');
        if (!href) return;
        if (href.indexOf('ventas/tickets/') !== -1 && href.match(/\/ventas\/tickets\/\d+(\?|$)/)) return;
        e.preventDefault();
        updateTabla(href);
    });
    bindAnular();
})();
</script>
@endpush
@endsection
