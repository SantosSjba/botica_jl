{{--
    Botón reutilizable con loader.
    Uso: el formulario (o contenedor) debe tener x-data="{ loading: false }" y @submit="loading = true"
    para que el loader se muestre al enviar.
    Ejemplo:
    <form x-data="{ loading: false }" @submit="loading = true" ...>
        <x-ui.button-loader type="submit" label="Iniciar sesión" loading-text="Cargando..." />
    </form>
--}}
<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $class]) }}
    :disabled="loading"
>
    {{-- Contenido normal --}}
    <span x-show="!loading" x-cloak x-transition class="inline-flex items-center justify-center gap-2">
        {{ $slot->isEmpty() ? $label : $slot }}
    </span>
    {{-- Estado cargando: spinner + texto --}}
    <span x-show="loading" x-cloak x-transition class="inline-flex items-center justify-center gap-2" style="display: none;">
        <svg class="animate-spin size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>{{ $loadingText ?? 'Cargando...' }}</span>
    </span>
</button>
