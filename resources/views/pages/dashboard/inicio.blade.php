@extends('layouts.app')

@section('content')
@php
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $fechaTexto = $dias[date('w')] . ' ' . date('d') . ' de ' . $meses[date('n') - 1] . ' del ' . date('Y');
@endphp
<div class="space-y-6">
    <x-common.page-breadcrumb pageTitle="Inicio" />

    {{-- Bienvenida --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ $fechaTexto }}</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Bienvenido <strong>{{ $usuario }}</strong>
            @if($razonSocial)
                · Razón social: <strong>{{ $razonSocial }}</strong>
            @endif
        </p>
    </div>

    {{-- Tiles: Caja, Compras, Clientes, Productos --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('en-desarrollo') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-200 hover:shadow-sm dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-800 md:p-6">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-900/30">
                <svg class="h-6 w-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Caja</p>
            <p class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">Apertura: {{ $simboloMoneda ?? 'S/' }} {{ number_format($montoCaja, 2) }}</p>
        </a>
        <a href="{{ route('compras.consulta.index') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-200 hover:shadow-sm dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-800 md:p-6">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Compras</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Consulta compras</p>
        </a>
        <a href="{{ route('mantenimiento.clientes.index') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-200 hover:shadow-sm dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-800 md:p-6">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Clientes</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Ir a clientes</p>
        </a>
        <a href="{{ route('en-desarrollo') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-200 hover:shadow-sm dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-800 md:p-6">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30">
                <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8 4-8-4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
            <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Productos</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Ir a productos</p>
        </a>
    </div>

    <x-ui.content-loading
        wrapperId="dashboard-datos-wrapper"
        loadingId="dashboard-datos-loading"
        contentContainerId="dashboard-datos-container"
        loadingText="Cargando..."
        contentContainerClass="space-y-6"
    >
        @include('pages.dashboard._datos-dashboard')
    </x-ui.content-loading>
</div>

@push('scripts')
<script>
(function() {
    var baseUrl = @json(route('dashboard'));
    var wrapper = document.getElementById('dashboard-datos-wrapper');
    var container = document.getElementById('dashboard-datos-container');
    var loadingEl = document.getElementById('dashboard-datos-loading');
    var form = document.getElementById('form-filtro-dashboard');

    function setLoading(show) {
        if (!loadingEl) return;
        if (show) { loadingEl.classList.remove('hidden'); loadingEl.classList.add('flex'); }
        else { loadingEl.classList.add('hidden'); loadingEl.classList.remove('flex'); }
    }

    function updateDatos(url, pushState) {
        if (!container) return;
        setLoading(true);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                container.innerHTML = html;
                if (pushState !== false) window.history.pushState({}, '', url);
                form = document.getElementById('form-filtro-dashboard');
                if (form) form.addEventListener('submit', onFormSubmit);
            })
            .catch(function() { window.location = url; })
            .finally(function() { setLoading(false); });
    }

    function onFormSubmit(e) {
        e.preventDefault();
        if (!form) return;
        var params = new URLSearchParams(new FormData(form));
        updateDatos(baseUrl + (params.toString() ? '?' + params.toString() : ''));
    }

    if (form) form.addEventListener('submit', onFormSubmit);
    wrapper.addEventListener('click', function(e) {
        var a = e.target.closest('a.dashboard-actualizar-link');
        if (a) {
            e.preventDefault();
            updateDatos(a.getAttribute('href'));
        }
    });
})();
</script>
@endpush
@endsection
