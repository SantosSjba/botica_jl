@extends('layouts.app')

@section('content')
@php
    $inputClass = 'shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    @if (session('success'))
        <div class="flash-toast" data-type="success" data-msg="{{ e(session('success')) }}" style="display:none" aria-hidden="true"></div>
    @endif
    @if (session('error'))
        <div class="flash-toast" data-type="error" data-msg="{{ e(session('error')) }}" style="display:none" aria-hidden="true"></div>
    @endif

    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('mantenimiento.productos.index') }}">
            <x-ui.button type="button" variant="outline" size="md">← Volver a productos</x-ui.button>
        </a>
        <a href="{{ route('mantenimiento.productos.edit', $producto) }}">
            <x-ui.button type="button" variant="outline" size="md">Editar producto</x-ui.button>
        </a>
    </div>

    <x-common.component-card title="Producto" :desc="$producto->descripcion">
        <div class="grid gap-2 text-theme-sm">
            <p><span class="font-medium text-gray-500 dark:text-gray-400">Presentación:</span> {{ $producto->presentacion?->presentacion ?? '—' }}</p>
            <p><span class="font-medium text-gray-500 dark:text-gray-400">Síntoma / Dolencia:</span> {{ $producto->sintoma?->sintoma ?? '—' }}</p>
        </div>
    </x-common.component-card>

    {{-- Similares por mismo síntoma --}}
    <x-common.component-card title="Similares por síntoma o dolencia" desc="Productos con el mismo síntoma asignado (misma dolencia).">
        @if ($porSintoma->isEmpty())
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">No hay otros productos con el mismo síntoma, o este producto no tiene síntoma asignado.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full min-w-[400px] text-left text-theme-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Descripción</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Presentación</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Stock</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">P. venta</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($porSintoma as $sim)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $sim->descripcion }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $sim->presentacion?->presentacion ?? '—' }}</td>
                                <td class="px-4 py-3">{{ $sim->stock }}</td>
                                <td class="px-4 py-3">{{ $simboloMoneda }} {{ number_format((float)$sim->precio_venta, 2) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('mantenimiento.productos.edit', $sim) }}" class="text-brand-600 hover:underline dark:text-brand-400">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-common.component-card>

    {{-- Agregar producto similar (manual) --}}
    <x-common.component-card title="Agregar producto similar" desc="Busque y agregue otro producto como similar (por descripción o código).">
        <form action="{{ route('mantenimiento.productos.similares.store', $producto) }}" method="post" class="space-y-4" id="form-agregar-similar">
            @csrf
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1 relative">
                    <label for="buscar-similar" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Buscar producto</label>
                    <input type="text" id="buscar-similar" autocomplete="off" placeholder="Escriba descripción o código (mín. 2 caracteres)"
                        class="{{ $inputClass }}" />
                    <input type="hidden" name="idproducto_similar" id="idproducto_similar" value="" />
                    <div id="lista-similares" class="absolute left-0 right-0 z-20 mt-1 max-h-48 overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800" style="display: none;"></div>
                </div>
                <x-ui.button type="submit" variant="primary" size="md" id="btn-agregar-similar" disabled>Agregar como similar</x-ui.button>
            </div>
        </form>
    </x-common.component-card>

    {{-- Lista de similares agregados manualmente --}}
    <x-common.component-card title="Similares agregados manualmente" desc="Productos que usted ha marcado como similares a este.">
        @if ($agregados->isEmpty())
            <p class="text-theme-sm text-gray-500 dark:text-gray-400">Aún no ha agregado ningún producto similar. Use el formulario de arriba para agregar.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                <table class="w-full min-w-[400px] text-left text-theme-sm">
                    <thead class="border-b border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">#</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Descripción</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Presentación</th>
                            <th class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($agregados as $idx => $row)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ $row->producto }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $row->presentacion }}</td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('mantenimiento.productos.similares.destroy', $producto) }}" method="post" class="inline" onsubmit="return confirm('¿Quitar este similar?');">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="producto" value="{{ e($row->producto) }}" />
                                        <input type="hidden" name="presentacion" value="{{ e($row->presentacion) }}" />
                                        <button type="submit" class="text-red-600 hover:underline dark:text-red-400">Quitar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-common.component-card>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var buscar = document.getElementById('buscar-similar');
    var idproductoSimilar = document.getElementById('idproducto_similar');
    var lista = document.getElementById('lista-similares');
    var btnAgregar = document.getElementById('btn-agregar-similar');
    var form = document.getElementById('form-agregar-similar');

    var timeout = null;
    if (buscar) {
        buscar.addEventListener('input', function() {
            var q = (this.value || '').trim();
            idproductoSimilar.value = '';
            btnAgregar.disabled = true;
            if (q.length < 2) {
                lista.style.display = 'none';
                lista.innerHTML = '';
                return;
            }
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                fetch('{{ route("mantenimiento.productos.buscar-para-similar") }}?q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        lista.innerHTML = '';
                        if (!data || data.length === 0) {
                            lista.innerHTML = '<div class="px-4 py-3 text-gray-500 dark:text-gray-400 text-sm">Sin resultados</div>';
                        } else {
                            data.forEach(function(p) {
                                var div = document.createElement('div');
                                div.className = 'cursor-pointer px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-sm';
                                div.textContent = p.descripcion + (p.presentacion ? ' — ' + p.presentacion : '');
                                div.dataset.id = p.idproducto;
                                div.dataset.desc = p.descripcion;
                                div.dataset.pres = p.presentacion || '';
                                div.addEventListener('click', function() {
                                    idproductoSimilar.value = this.dataset.id;
                                    buscar.value = this.dataset.desc + (this.dataset.pres ? ' — ' + this.dataset.pres : '');
                                    lista.style.display = 'none';
                                    btnAgregar.disabled = false;
                                });
                                lista.appendChild(div);
                            });
                        }
                        lista.style.display = 'block';
                    })
                    .catch(function() {
                        lista.innerHTML = '<div class="px-4 py-3 text-red-500 text-sm">Error al buscar</div>';
                        lista.style.display = 'block';
                    });
            }, 300);
        });
    }

    document.addEventListener('click', function(e) {
        if (lista && buscar && !lista.contains(e.target) && e.target !== buscar) {
            lista.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection
