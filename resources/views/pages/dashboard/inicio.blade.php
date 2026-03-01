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
        <a href="{{ route('en-desarrollo') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-200 hover:shadow-sm dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-800 md:p-6">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900/30">
                <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Compras</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Ir a compras</p>
        </a>
        <a href="{{ route('en-desarrollo') }}" class="rounded-2xl border border-gray-200 bg-white p-5 transition hover:border-brand-200 hover:shadow-sm dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-800 md:p-6">
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

    {{-- Resumen Financiero --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Resumen Financiero</h3>
        <form method="get" action="{{ route('dashboard') }}" class="mt-4 flex flex-wrap items-end gap-3" x-data="{ loading: false }" @submit="loading = true">
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Desde</label>
                <input type="date" name="fecha_desde" value="{{ $fechaDesde }}" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ $fechaHasta }}" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div class="min-w-[180px]">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Usuario</label>
                <div x-data="{ isOptionSelected: true }" class="relative z-20 bg-transparent">
                    <select name="filtro_usuario" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                        :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                        <option value="0" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" {{ $filtroUsuario == 0 ? 'selected' : '' }}>Todos (General)</option>
                        @foreach($listaUsuarios as $u)
                            <option value="{{ $u->idusu }}" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" {{ $filtroUsuario == $u->idusu ? 'selected' : '' }}>{{ $u->nombres ?: $u->usuario }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
            </div>
            <x-ui.button-loader type="submit" label="Filtrar" loading-text="Cargando..." class="h-11 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-brand-600 disabled:opacity-70 disabled:cursor-not-allowed" />
            <a href="{{ route('dashboard') }}" class="inline-flex h-11 items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">Mes actual</a>
        </form>
        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
            <strong>Período:</strong> {{ date('d/m/Y', strtotime($fechaDesde)) }} – {{ date('d/m/Y', strtotime($fechaHasta)) }}
            @if($filtroUsuario > 0)
                @php $nomUser = $listaUsuarios->firstWhere('idusu', $filtroUsuario); @endphp
                · <strong>Usuario:</strong> {{ $nomUser ? ($nomUser->nombres ?: $nomUser->usuario) : '—' }}
            @else
                · <strong>Vista:</strong> Total empresa
            @endif
        </p>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-center dark:border-gray-800 dark:bg-gray-800/50">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Ventas</p>
                <p class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $simboloMoneda ?? 'S/' }} {{ number_format($ventas, 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-center dark:border-gray-800 dark:bg-gray-800/50">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Costo</p>
                <p class="mt-1 text-lg font-bold text-gray-800 dark:text-white/90">{{ $simboloMoneda ?? 'S/' }} {{ number_format($costos, 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-center dark:border-gray-800 dark:bg-gray-800/50">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Ganancia</p>
                <p class="mt-1 text-lg font-bold text-green-600 dark:text-green-400">{{ $simboloMoneda ?? 'S/' }} {{ number_format($ganancia, 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 text-center dark:border-gray-800 dark:bg-gray-800/50">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Gastos</p>
                <p class="mt-1 text-lg font-bold text-red-600 dark:text-red-400">{{ $simboloMoneda ?? 'S/' }} {{ number_format($gastos, 2) }}</p>
            </div>
            <div class="rounded-xl border-2 border-green-200 bg-green-50 p-4 text-center dark:border-green-800 dark:bg-green-900/20">
                <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Neto</p>
                <p class="mt-1 text-lg font-bold text-green-700 dark:text-green-400">{{ $simboloMoneda ?? 'S/' }} {{ number_format($neto, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Productos por vencer (14 días) o vencidos --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Productos por vencer (14 días) o vencidos</h3>
            <a href="{{ route('dashboard', request()->query()) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03] dark:hover:text-gray-200" title="Actualizar datos">
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Actualizar
            </a>
        </div>
        <div class="max-w-full overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="py-3 text-left pl-4 sm:pl-6"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">#</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Código</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Descripción</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Fec. vencimiento</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">P. venta</p></th>
                        <th class="py-3 text-left pr-4 sm:pr-6"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Estado</p></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productosPorVencer as $i => $p)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 pl-4 sm:pl-6 text-sm text-gray-800 dark:text-white/90">{{ $i + 1 }}</td>
                            <td class="py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->codigo }}</td>
                            <td class="py-3 text-sm text-gray-800 dark:text-white/90">{{ $p->descripcion }}</td>
                            <td class="py-3">
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ $p->fecha_vencimiento ? \Carbon\Carbon::parse($p->fecha_vencimiento)->format('d/m/Y') : '—' }}</span>
                            </td>
                            <td class="py-3 text-sm text-gray-600 dark:text-gray-400">{{ $simboloMoneda ?? 'S/' }} {{ number_format($p->precio_venta, 2) }}</td>
                            <td class="py-3 pr-4 sm:pr-6">
                                @if($p->estado == '1')
                                    <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">Activo</span>
                                @else
                                    <span class="rounded-full bg-error-50 px-2 py-0.5 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">No hay productos por vencer o vencidos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Productos con bajo stock --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-4 dark:border-gray-800 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Productos con bajo stock</h3>
            <a href="{{ route('dashboard', request()->query()) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03] dark:hover:text-gray-200" title="Actualizar datos">
                <svg class="size-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Actualizar
            </a>
        </div>
        <div class="max-w-full overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="py-3 text-left pl-4 sm:pl-6"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">#</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Código</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Descripción</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Fec. vencimiento</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Stock</p></th>
                        <th class="py-3 text-left"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">P. venta</p></th>
                        <th class="py-3 text-left pr-4 sm:pr-6"><p class="text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Estado</p></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productosBajoStock as $i => $p)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="py-3 pl-4 sm:pl-6 text-sm text-gray-800 dark:text-white/90">{{ $i + 1 }}</td>
                            <td class="py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->codigo }}</td>
                            <td class="py-3 text-sm text-gray-800 dark:text-white/90">{{ $p->descripcion }}</td>
                            <td class="py-3 text-sm text-gray-600 dark:text-gray-400">{{ $p->fecha_vencimiento ? \Carbon\Carbon::parse($p->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                            <td class="py-3">
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">{{ $p->stock }}</span>
                            </td>
                            <td class="py-3 text-sm text-gray-600 dark:text-gray-400">{{ $simboloMoneda ?? 'S/' }} {{ number_format($p->precio_venta, 2) }}</td>
                            <td class="py-3 pr-4 sm:pr-6">
                                @if($p->estado == '1')
                                    <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">Activo</span>
                                @else
                                    <span class="rounded-full bg-error-50 px-2 py-0.5 text-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">No hay productos con bajo stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
