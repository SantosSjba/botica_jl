@extends('layouts.app')

@section('content')
@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $inputReadonlyClass = 'shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white/90 dark:placeholder:text-white/30';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
    $errorClass = ' border-red-500 dark:border-red-500';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    @if ($errors->any())
        <div class="flash-toast" data-type="error" data-msg="{{ e(implode(' ', $errors->all())) }}" style="display:none" aria-hidden="true"></div>
    @endif

    <x-common.component-card
        title="Configuración"
        desc="Datos de la empresa, ubicación, IGV, moneda y credenciales SUNAT (Usuario SOL). El logo se muestra en el sistema."
    >
        <form action="{{ route('configuracion.update') }}" method="post" enctype="multipart/form-data" class="form-ajax-submit space-y-6" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="razon_social" class="{{ $labelClass }}">Razón social <span class="text-red-500">*</span></label>
                    <input type="text" id="razon_social" name="razon_social" value="{{ old('razon_social', $config->razon_social) }}" required maxlength="255" placeholder="Razón social"
                        class="{{ $inputClass }} @error('razon_social'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="nombre_comercial" class="{{ $labelClass }}">Nombre comercial <span class="text-red-500">*</span></label>
                    <input type="text" id="nombre_comercial" name="nombre_comercial" value="{{ old('nombre_comercial', $config->nombre_comercial) }}" required maxlength="255" placeholder="Nombre comercial"
                        class="{{ $inputClass }} @error('nombre_comercial'){{ $errorClass }}@enderror" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="ruc" class="{{ $labelClass }}">RUC <span class="text-red-500">*</span></label>
                    <input type="text" id="ruc" name="ruc" value="{{ old('ruc', $config->ruc) }}" required maxlength="20" placeholder="RUC"
                        class="{{ $inputClass }} @error('ruc'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="direccion" class="{{ $labelClass }}">Dirección <span class="text-red-500">*</span></label>
                    <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $config->direccion) }}" required maxlength="255" placeholder="Dirección"
                        class="{{ $inputClass }} @error('direccion'){{ $errorClass }}@enderror" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="departamento" class="{{ $labelClass }}">Departamento <span class="text-red-500">*</span></label>
                    <input type="text" id="departamento" name="departamento" value="{{ old('departamento', $config->departamento) }}" required maxlength="100"
                        class="{{ $inputClass }} @error('departamento'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="provincia" class="{{ $labelClass }}">Provincia <span class="text-red-500">*</span></label>
                    <input type="text" id="provincia" name="provincia" value="{{ old('provincia', $config->provincia) }}" required maxlength="100"
                        class="{{ $inputClass }} @error('provincia'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="distrito" class="{{ $labelClass }}">Distrito <span class="text-red-500">*</span></label>
                    <input type="text" id="distrito" name="distrito" value="{{ old('distrito', $config->distrito) }}" required maxlength="100"
                        class="{{ $inputClass }} @error('distrito'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="ubigeo" class="{{ $labelClass }}">Ubigeo (6 dígitos) <span class="text-red-500">*</span></label>
                    <input type="text" id="ubigeo" name="ubigeo" value="{{ old('ubigeo', $config->ubigeo) }}" required maxlength="6" minlength="6" placeholder="000000"
                        class="{{ $inputClass }} @error('ubigeo'){{ $errorClass }}@enderror" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label for="impuesto" class="{{ $labelClass }}">IGV % <span class="text-red-500">*</span></label>
                    <input type="number" id="impuesto" name="impuesto" value="{{ old('impuesto', $config->impuesto) }}" required min="0" max="100" step="0.01"
                        class="{{ $inputClass }} @error('impuesto'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="simbolo_moneda" class="{{ $labelClass }}">Símbolo moneda <span class="text-red-500">*</span></label>
                    <input type="text" id="simbolo_moneda" name="simbolo_moneda" value="{{ old('simbolo_moneda', $config->simbolo_moneda ?? 'S/') }}" required maxlength="10" placeholder="S/"
                        class="{{ $inputClass }} @error('simbolo_moneda'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="usuario_sol" class="{{ $labelClass }}">Usuario SOL <span class="text-red-500">*</span></label>
                    <input type="text" id="usuario_sol" name="usuario_sol" value="{{ old('usuario_sol', $config->usuario_sol) }}" required maxlength="50" placeholder="Usuario SUNAT"
                        class="{{ $inputClass }} @error('usuario_sol'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="clave_sol" class="{{ $labelClass }}">Clave SOL</label>
                    <input type="password" id="clave_sol" name="clave_sol" value="" maxlength="50" placeholder="Dejar en blanco para no cambiar"
                        class="{{ $inputClass }} @error('clave_sol'){{ $errorClass }}@enderror" autocomplete="off" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Dejar en blanco para mantener la actual.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="imagen" class="{{ $labelClass }}">Logo (JPG o PNG, máx. 2 MB)</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/jpg"
                        class="block w-full text-sm text-gray-800 file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/20 dark:file:text-brand-300 dark:hover:file:bg-brand-500/30" />
                </div>
                <div>
                    @if($logoUrl)
                        <p class="{{ $labelClass }}">Logo actual</p>
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-28 w-auto rounded-lg border border-gray-200 object-contain dark:border-gray-700" />
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">No hay logo cargado.</p>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <x-ui.button-loader type="submit" label="Guardar configuración" loading-text="Guardando..." class="!px-5 !py-3.5 bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 rounded-lg font-medium text-sm" />
                <a href="{{ route('dashboard') }}"><x-ui.button type="button" variant="outline" size="md">Cancelar</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
</div>
@endsection
