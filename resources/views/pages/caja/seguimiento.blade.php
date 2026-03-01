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

    <x-common.component-card
        :title="'Seguimiento de caja' . ($esAdministrador ? ' (Todos los cajeros)' : '')"
        desc="Listado de aperturas de caja. Filtre por fecha y usuario (solo administrador)."
    >
        <div class="space-y-4">
            <form id="form-filtro-caja" method="get" action="{{ route('caja.seguimiento') }}" class="flex flex-wrap items-end gap-3">
                <div>
                    <label for="filtro_fecha" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fecha</label>
                    <input type="date" id="filtro_fecha" name="filtro_fecha" value="{{ $filtroFecha }}"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>
                @if($esAdministrador)
                    <div>
                        <label for="filtro_usuario" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Usuario</label>
                        <select id="filtro_usuario" name="filtro_usuario"
                            class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 min-w-[140px] rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">Todos</option>
                            @foreach($listaUsuarios as $u)
                                <option value="{{ $u }}" {{ $filtroUsuario === $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <x-ui.button type="submit" variant="primary" size="md" class="shrink-0">Filtrar</x-ui.button>
            </form>

            <x-ui.content-loading
                wrapperId="caja-seguimiento-wrapper"
                loadingId="caja-seguimiento-loading"
                contentContainerId="caja-seguimiento-tabla-container"
            >
                @include('pages.caja._tabla-seguimiento')
            </x-ui.content-loading>
        </div>
    </x-common.component-card>
</div>

@push('scripts')
<script>
(function() {
    var baseUrl = @json(route('caja.seguimiento'));
    var wrapper = document.getElementById('caja-seguimiento-wrapper');
    var container = document.getElementById('caja-seguimiento-tabla-container');
    var loadingEl = document.getElementById('caja-seguimiento-loading');
    var form = document.getElementById('form-filtro-caja');

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
            })
            .catch(function() { window.location = url; })
            .finally(function() { setLoading(false); });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        updateTabla(buildUrl());
    });

    wrapper.addEventListener('click', function(e) {
        var a = e.target.closest('a[href*="caja/seguimiento"]');
        if (!a) return;
        var href = a.getAttribute('href');
        if (href === baseUrl || href.indexOf(baseUrl + '?') === 0) {
            e.preventDefault();
            updateTabla(href);
        }
    });
})();
</script>
@endpush
@endsection
