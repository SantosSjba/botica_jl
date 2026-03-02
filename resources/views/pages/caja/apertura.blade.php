@extends('layouts.app')

@section('content')
@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $inputReadonlyClass = 'shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white/90 dark:placeholder:text-white/30';
    $selectClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
    $errorClass = ' border-red-500 dark:border-red-500';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    @if ($errors->any())
        <div class="flash-toast" data-type="error" data-msg="{{ e(implode(' ', $errors->all())) }}" style="display:none" aria-hidden="true"></div>
    @endif

    <x-common.component-card title="Apertura de caja" desc="Registre el monto inicial y el turno para aperturar la caja.">
        <form action="{{ route('caja.store-apertura') }}" method="post" class="form-ajax-submit space-y-6" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            {{-- Cajero (solo lectura) --}}
            <div>
                <label class="{{ $labelClass }}">Cajero</label>
                <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $usuario }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Fecha (readonly) --}}
                <div>
                    <label for="txtfec" class="{{ $labelClass }}">Fecha</label>
                    <input type="text" id="txtfec" name="txtfec" value="{{ $fecha }}" readonly
                        class="{{ $inputReadonlyClass }}" />
                </div>
                {{-- Caja (select TailAdmin) --}}
                <div>
                    <label for="txtcaja" class="{{ $labelClass }}">Caja <span class="text-red-500">*</span></label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="txtcaja" name="txtcaja" required
                            class="{{ $selectClass }} @error('txtcaja'){{ $errorClass }}@enderror"
                            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            @foreach($cajas as $c)
                                <option value="{{ $c }}" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" {{ old('txtcaja') === $c ? 'selected' : '' }}>{{ strtoupper($c) }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Turno (select TailAdmin) --}}
                <div>
                    <label for="txtturno" class="{{ $labelClass }}">Turno <span class="text-red-500">*</span></label>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select id="txtturno" name="txtturno" required
                            class="{{ $selectClass }} @error('txtturno'){{ $errorClass }}@enderror"
                            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'" @change="isOptionSelected = true">
                            @foreach($turnos as $t)
                                <option value="{{ $t }}" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" {{ old('txtturno') === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                </div>
                {{-- Hora (readonly) --}}
                <div>
                    <label for="txthor" class="{{ $labelClass }}">Hora</label>
                    <input type="text" id="txthor" name="txthor" value="{{ $hora }}" readonly
                        class="{{ $inputReadonlyClass }}" />
                </div>
            </div>

            {{-- Monto de apertura --}}
            <div class="max-w-xs">
                <label for="txtmon" class="{{ $labelClass }}">Monto de apertura <span class="text-red-500">*</span></label>
                <input type="number" id="txtmon" name="txtmon" value="{{ old('txtmon') }}" min="0" step="0.01" required
                    placeholder="Ingrese el monto para la apertura de la caja"
                    class="{{ $inputClass }} @error('txtmon'){{ $errorClass }}@enderror" />
            </div>

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <x-ui.button-loader type="submit" label="Aperturar caja" loading-text="Guardando..." class="!px-5 !py-3.5 bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 rounded-lg font-medium text-sm" />
                <a href="{{ route('caja.seguimiento') }}"><x-ui.button type="button" variant="outline" size="md">Cancelar</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
</div>
@push('scripts')
<script>
document.getElementById('txtmon').addEventListener('blur', function() {
    var v = parseFloat(this.value);
    if (!isNaN(v)) this.value = v.toFixed(2);
});
</script>
@endpush
@endsection
