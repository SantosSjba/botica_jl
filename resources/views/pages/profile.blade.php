@extends('layouts.app')

@php
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
    $inputReadonlyClass = 'shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

@section('content')
    <div class="min-w-0 space-y-6">
        <x-common.page-breadcrumb :pageTitle="$title" />

        @php $user = $user ?? auth()->user(); @endphp
        @if($user)
            <x-common.component-card title="Información del usuario" desc="Datos de tu cuenta en el sistema.">
                <div class="space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">Usuario</label>
                            <input type="text" value="{{ old('usuario', $user->usuario) }}" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Nombres</label>
                            <input type="text" value="{{ old('nombres', $user->nombres) }}" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">Correo electrónico</label>
                            <input type="text" value="{{ old('email', $user->email) }}" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Teléfono</label>
                            <input type="text" value="{{ old('telefono', $user->telefono) }}" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">Cargo</label>
                            <input type="text" value="{{ old('cargo_usu', $user->cargo_usu) }}" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Tipo</label>
                            <input type="text" value="{{ old('tipo', $user->tipo) }}" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="{{ $labelClass }}">Fecha de ingreso</label>
                            <input type="text" value="{{ $user->fechaingreso ? \Carbon\Carbon::parse($user->fechaingreso)->format('d/m/Y') : '—' }}" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                        <div>
                            <label class="{{ $labelClass }}">Estado</label>
                            <input type="text" value="{{ $user->estado ?? '—' }}" readonly class="{{ $inputReadonlyClass }}" />
                        </div>
                    </div>
                </div>
            </x-common.component-card>
        @else
            <x-common.component-card title="Perfil" desc="No hay sesión activa.">
                <p class="text-sm text-gray-600 dark:text-gray-400">Inicia sesión para ver tu perfil.</p>
            </x-common.component-card>
        @endif
    </div>
@endsection
