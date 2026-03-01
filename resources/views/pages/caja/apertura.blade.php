@extends('layouts.app')

@section('content')
@php
    $inputClass = 'shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $errorClass = ' border-red-500 dark:border-red-500';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    @if (session('error'))
        <x-ui.alert variant="error" :message="session('error')" />
    @endif

    <x-common.component-card title="Apertura de caja" desc="Registre el monto inicial y el turno para aperturar la caja.">
        <form action="{{ route('caja.store-apertura') }}" method="post" x-data="{ loading: false }" @submit="loading = true" class="space-y-6">
            @csrf
            @if ($errors->any())
                <x-ui.alert variant="error" :message="implode(' ', $errors->all())" />
            @endif

            <div class="rounded-lg bg-gray-50 dark:bg-gray-800/50 px-4 py-3">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Cajero: <span class="text-gray-800 dark:text-white/90">{{ $usuario }}</span></p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="txtfec" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fecha</label>
                    <input type="text" id="txtfec" name="txtfec" value="{{ $fecha }}" readonly
                        class="{{ $inputClass }} bg-gray-100 dark:bg-gray-800" />
                </div>
                <div>
                    <label for="txtcaja" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Caja <span class="text-red-500">*</span></label>
                    <select id="txtcaja" name="txtcaja" required class="{{ $inputClass }} @error('txtcaja'){{ $errorClass }}@enderror">
                        @foreach($cajas as $c)
                            <option value="{{ $c }}" {{ old('txtcaja') === $c ? 'selected' : '' }}>{{ strtoupper($c) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="txtturno" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Turno <span class="text-red-500">*</span></label>
                    <select id="txtturno" name="txtturno" required class="{{ $inputClass }} @error('txtturno'){{ $errorClass }}@enderror">
                        @foreach($turnos as $t)
                            <option value="{{ $t }}" {{ old('txtturno') === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="txthor" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Hora</label>
                    <input type="text" id="txthor" name="txthor" value="{{ $hora }}" readonly
                        class="{{ $inputClass }} bg-gray-100 dark:bg-gray-800" />
                </div>
            </div>

            <div class="max-w-xs">
                <label for="txtmon" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Monto de apertura <span class="text-red-500">*</span></label>
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
