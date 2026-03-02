@extends('layouts.app')

@section('content')
@php
    $inputClass = 'shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $errorClass = ' border-red-500 dark:border-red-500';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />
    @if ($errors->any())
        <div class="flash-toast" data-type="error" data-msg="{{ e(implode(' ', $errors->all())) }}" style="display:none" aria-hidden="true"></div>
    @endif

    <x-common.component-card title="Actualizar Lote" desc="(*) Campos obligatorios">
        <form action="{{ route('mantenimiento.lotes.update', $lote) }}" method="post" x-data="{ loading: false }" @submit="loading = true" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="numero" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Número de lote <span class="text-red-500">*</span></label>
                    <input type="text" id="numero" name="numero" value="{{ old('numero', $lote->numero) }}" required placeholder="Ej. 001, SIN LOTE"
                        class="{{ $inputClass }} @error('numero'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="fecha_vencimiento" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fecha vencimiento</label>
                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('Y-m-d') : '') }}"
                        class="{{ $inputClass }} @error('fecha_vencimiento'){{ $errorClass }}@enderror" />
                </div>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <x-ui.button-loader type="submit" label="Modificar" loading-text="Guardando..." class="!px-5 !py-3.5 bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 rounded-lg font-medium text-sm" />
                <a href="{{ route('mantenimiento.lotes.index') }}"><x-ui.button type="button" variant="outline" size="md">Cancelar</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
</div>
@endsection
