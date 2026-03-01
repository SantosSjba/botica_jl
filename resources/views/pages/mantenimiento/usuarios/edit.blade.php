@extends('layouts.app')

@section('content')
@php
    $inputClass = 'shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $errorClass = ' border-red-500 dark:border-red-500';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card title="Actualizar Usuario" desc="(*) Campos obligatorios. Deje la contraseña en blanco para no cambiarla.">
        <form action="{{ route('mantenimiento.usuarios.update', $usuario) }}" method="post" x-data="{ loading: false }" @submit="loading = true" class="space-y-6">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <x-ui.alert variant="error" :message="implode(' ', $errors->all())" />
            @endif
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nombres" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nombres <span class="text-red-500">*</span></label>
                    <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $usuario->nombres) }}" required placeholder="Nombres completos"
                        class="{{ $inputClass }} @error('nombres'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" placeholder="correo@ejemplo.com"
                        class="{{ $inputClass }} @error('email'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="telefono" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" placeholder="Teléfono"
                        class="{{ $inputClass }} @error('telefono'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="fechaingreso" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fecha de ingreso <span class="text-red-500">*</span></label>
                    <input type="date" id="fechaingreso" name="fechaingreso" value="{{ old('fechaingreso', $usuario->fechaingreso ? \Carbon\Carbon::parse($usuario->fechaingreso)->format('Y-m-d') : '') }}" required
                        class="{{ $inputClass }} @error('fechaingreso'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="cargo_usu" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cargo <span class="text-red-500">*</span></label>
                    <input type="text" id="cargo_usu" name="cargo_usu" value="{{ old('cargo_usu', $usuario->cargo_usu) }}" required placeholder="Cargo"
                        class="{{ $inputClass }} @error('cargo_usu'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="estado" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Estado <span class="text-red-500">*</span></label>
                    <select id="estado" name="estado" required class="{{ $inputClass }} @error('estado'){{ $errorClass }}@enderror">
                        <option value="Activo" {{ old('estado', $usuario->estado) === 'Activo' ? 'selected' : '' }}>Activo</option>
                        <option value="Inactivo" {{ old('estado', $usuario->estado) === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div>
                    <label for="tipo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo <span class="text-red-500">*</span></label>
                    <select id="tipo" name="tipo" required class="{{ $inputClass }} @error('tipo'){{ $errorClass }}@enderror">
                        <option value="USUARIO" {{ old('tipo', $usuario->tipo) === 'USUARIO' ? 'selected' : '' }}>USUARIO</option>
                        <option value="ADMINISTRADOR" {{ old('tipo', $usuario->tipo) === 'ADMINISTRADOR' ? 'selected' : '' }}>ADMINISTRADOR</option>
                    </select>
                </div>
                <div>
                    <label for="usuario" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Usuario (login) <span class="text-red-500">*</span></label>
                    <input type="text" id="usuario" name="usuario" value="{{ old('usuario', $usuario->usuario) }}" required placeholder="Nombre de usuario"
                        class="{{ $inputClass }} @error('usuario'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="clave" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nueva contraseña</label>
                    <input type="password" id="clave" name="clave" placeholder="Dejar en blanco para no cambiar"
                        class="{{ $inputClass }} @error('clave'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="clave_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Confirmar contraseña</label>
                    <input type="password" id="clave_confirmation" name="clave_confirmation" placeholder="Solo si cambia contraseña"
                        class="{{ $inputClass }}" />
                </div>
            </div>
            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <x-ui.button-loader type="submit" label="Modificar" loading-text="Guardando..." class="!px-5 !py-3.5 bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 rounded-lg font-medium text-sm" />
                <a href="{{ route('mantenimiento.usuarios.index') }}"><x-ui.button type="button" variant="outline" size="md">Cancelar</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
</div>
@endsection
