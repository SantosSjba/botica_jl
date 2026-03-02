@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />
    @if ($errors->any())
        <div class="flash-toast" data-type="error" data-msg="{{ e(implode(' ', $errors->all())) }}" style="display:none" aria-hidden="true"></div>
    @endif

    <x-common.component-card title="Registrar Cliente / Laboratorio" desc="(*) Campos obligatorios">
        <form action="{{ route('mantenimiento.clientes.store') }}" method="post" class="form-ajax-submit space-y-6" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nombres" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Razón social <span class="text-red-500">*</span></label>
                    <input type="text" id="nombres" name="nombres" value="{{ old('nombres') }}" required
                        placeholder="Ingrese razón social"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('nombres') border-red-500 dark:border-red-500 @enderror" />
                </div>
                <div>
                    <label for="direccion" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}"
                        placeholder="Ingrese dirección"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('direccion') border-red-500 dark:border-red-500 @enderror" />
                </div>
                <div>
                    <label for="id_tipo_docu" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo documento <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="id_tipo_docu" name="id_tipo_docu" required
                            class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('id_tipo_docu') border-red-500 dark:border-red-500 @enderror">
                            @foreach($tiposDocumento as $td)
                                <option value="{{ $td->idtipo_docu }}" {{ old('id_tipo_docu') == $td->idtipo_docu ? 'selected' : '' }}>{{ $td->descripcion }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="nrodoc" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">N. documento <span class="text-red-500">*</span></label>
                    <input type="text" id="nrodoc" name="nrodoc" value="{{ old('nrodoc') }}" required
                        placeholder="DNI 8 dígitos, RUC 11 dígitos"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('nrodoc') border-red-500 dark:border-red-500 @enderror" />
                </div>
                <div>
                    <label for="tipo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="tipo" name="tipo" required
                            class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('tipo') border-red-500 dark:border-red-500 @enderror">
                            <option value="cliente" {{ old('tipo', 'cliente') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                            <option value="laboratorio" {{ old('tipo') === 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <x-ui.button-loader type="submit" label="Registrar" loading-text="Guardando..." class="!px-5 !py-3.5 bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 rounded-lg font-medium text-sm" />
                <a href="{{ route('mantenimiento.clientes.index') }}"><x-ui.button type="button" variant="outline" size="md">Cancelar</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
</div>
@endsection
