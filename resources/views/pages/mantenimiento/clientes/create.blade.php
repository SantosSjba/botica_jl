@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="border-b border-gray-200 px-4 py-4 dark:border-gray-800 sm:px-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Registrar Cliente / Laboratorio</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">(*) Campos obligatorios</p>
        </div>
        <form action="{{ route('mantenimiento.clientes.store') }}" method="post" x-data="{ loading: false }" @submit="loading = true" class="p-4 sm:p-6">
            @csrf
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nombres" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Razón social <span class="text-red-500">*</span></label>
                    <input type="text" id="nombres" name="nombres" value="{{ old('nombres') }}" required
                        placeholder="Ingrese razón social"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('nombres') border-red-500 @enderror" />
                </div>
                <div>
                    <label for="direccion" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}"
                        placeholder="Ingrese dirección"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('direccion') border-red-500 @enderror" />
                </div>
                <div>
                    <label for="id_tipo_docu" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo documento <span class="text-red-500">*</span></label>
                    <select id="id_tipo_docu" name="id_tipo_docu" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('id_tipo_docu') border-red-500 @enderror">
                        @foreach($tiposDocumento as $td)
                            <option value="{{ $td->idtipo_docu }}" {{ old('id_tipo_docu') == $td->idtipo_docu ? 'selected' : '' }}>{{ $td->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="nrodoc" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">N. documento <span class="text-red-500">*</span></label>
                    <input type="text" id="nrodoc" name="nrodoc" value="{{ old('nrodoc') }}" required
                        placeholder="Ingrese número de documento (DNI 8 dígitos, RUC 11 dígitos)"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('nrodoc') border-red-500 @enderror" />
                </div>
                <div>
                    <label for="tipo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo <span class="text-red-500">*</span></label>
                    <select id="tipo" name="tipo" required
                        class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 @error('tipo') border-red-500 @enderror">
                        <option value="cliente" {{ old('tipo', 'cliente') === 'cliente' ? 'selected' : '' }}>Cliente</option>
                        <option value="laboratorio" {{ old('tipo') === 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                    </select>
                </div>
            </div>
            <div class="mt-6 flex flex-wrap gap-3">
                <x-ui.button-loader type="submit" label="Registrar" loading-text="Guardando..." class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600" />
                <a href="{{ route('mantenimiento.clientes.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection
