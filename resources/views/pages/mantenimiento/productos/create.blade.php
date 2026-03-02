@extends('layouts.app')

@section('content')
@php
    $inputClass = 'shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $selectClass = $inputClass . ' appearance-none pr-11';
    $errorClass = ' border-red-500 dark:border-red-500';
@endphp
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />
    @if ($errors->any())
        <div class="flash-toast" data-type="error" data-msg="{{ e(implode(' ', $errors->all())) }}" style="display:none" aria-hidden="true"></div>
    @endif

    <x-common.component-card title="Registrar Producto" desc="(*) Campos obligatorios">
        <form action="{{ route('mantenimiento.productos.store') }}" method="post" x-data="{ loading: false }" @submit="loading = true" class="space-y-6">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="codigo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Código de barra</label>
                    <input type="text" id="codigo" name="codigo" value="{{ old('codigo') }}" placeholder="Código de barra"
                        class="{{ $inputClass }} @error('codigo'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="idlote" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Lote <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="idlote" name="idlote" required class="{{ $selectClass }} @error('idlote'){{ $errorClass }}@enderror">
                            @foreach($lotes as $l)
                                <option value="{{ $l->idlote }}" data-fecha="{{ $l->fecha_vencimiento ? $l->fecha_vencimiento->format('Y-m-d') : '' }}" {{ old('idlote') == $l->idlote ? 'selected' : '' }}>{{ $l->numero }} @if($l->fecha_vencimiento)(Vence: {{ $l->fecha_vencimiento->format('d/m/Y') }})@endif</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="fecha_vencimiento" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fecha venc. lote</label>
                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}"
                        class="{{ $inputClass }} @error('fecha_vencimiento'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="descripcion" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Descripción <span class="text-red-500">*</span></label>
                    <input type="text" id="descripcion" name="descripcion" value="{{ old('descripcion') }}" required placeholder="Descripción del producto"
                        class="{{ $inputClass }} @error('descripcion'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="tipo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="tipo" name="tipo" required class="{{ $selectClass }} @error('tipo'){{ $errorClass }}@enderror">
                            <option value="Generico" {{ old('tipo', 'Generico') === 'Generico' ? 'selected' : '' }}>Genérico</option>
                            <option value="No Generico" {{ old('tipo') === 'No Generico' ? 'selected' : '' }}>No genérico</option>
                            <option value="No Aplica" {{ old('tipo') === 'No Aplica' ? 'selected' : '' }}>No aplica</option>
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="stock" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock <span class="text-red-500">*</span></label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" required min="0"
                        class="{{ $inputClass }} @error('stock'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="stockminimo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Stock mínimo <span class="text-red-500">*</span></label>
                    <input type="number" id="stockminimo" name="stockminimo" value="{{ old('stockminimo', 0) }}" required min="0"
                        class="{{ $inputClass }} @error('stockminimo'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="precio_compra" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Precio compra <span class="text-red-500">*</span></label>
                    <input type="number" id="precio_compra" name="precio_compra" value="{{ old('precio_compra', '0.00') }}" required min="0" step="0.01"
                        class="{{ $inputClass }} @error('precio_compra'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="precio_venta" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Precio venta <span class="text-red-500">*</span></label>
                    <input type="number" id="precio_venta" name="precio_venta" value="{{ old('precio_venta', '0.00') }}" required min="0" step="0.01"
                        class="{{ $inputClass }} @error('precio_venta'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="ventasujeta" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Venta sujeta <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="ventasujeta" name="ventasujeta" required class="{{ $selectClass }} @error('ventasujeta'){{ $errorClass }}@enderror">
                            <option value="sin receta medica" {{ old('ventasujeta', 'sin receta medica') === 'sin receta medica' ? 'selected' : '' }}>Sin receta médica</option>
                            <option value="Con receta medica" {{ old('ventasujeta') === 'Con receta medica' ? 'selected' : '' }}>Con receta médica</option>
                            <option value="No aplica" {{ old('ventasujeta') === 'No aplica' ? 'selected' : '' }}>No aplica</option>
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="fecha_registro" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fecha registro <span class="text-red-500">*</span></label>
                    <input type="date" id="fecha_registro" name="fecha_registro" value="{{ old('fecha_registro', date('Y-m-d')) }}" required
                        class="{{ $inputClass }} @error('fecha_registro'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="reg_sanitario" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Reg. sanitario</label>
                    <input type="text" id="reg_sanitario" name="reg_sanitario" value="{{ old('reg_sanitario') }}" placeholder="Registro sanitario"
                        class="{{ $inputClass }} @error('reg_sanitario'){{ $errorClass }}@enderror" />
                </div>
                <div>
                    <label for="idcategoria" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Forma farmacéutica <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="idcategoria" name="idcategoria" required class="{{ $selectClass }} @error('idcategoria'){{ $errorClass }}@enderror">
                            @foreach($categorias as $c)
                                <option value="{{ $c->idcategoria }}" {{ old('idcategoria') == $c->idcategoria ? 'selected' : '' }}>{{ $c->forma_farmaceutica }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="idpresentacion" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Presentación <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="idpresentacion" name="idpresentacion" required class="{{ $selectClass }} @error('idpresentacion'){{ $errorClass }}@enderror">
                            @foreach($presentaciones as $pr)
                                <option value="{{ $pr->idpresentacion }}" {{ old('idpresentacion') == $pr->idpresentacion ? 'selected' : '' }}>{{ $pr->presentacion }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="idcliente" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Laboratorio</label>
                    <div class="relative z-20 bg-transparent">
                        <select id="idcliente" name="idcliente" class="{{ $selectClass }} @error('idcliente'){{ $errorClass }}@enderror">
                            <option value="">— Seleccione —</option>
                            @foreach($laboratorios as $lab)
                                <option value="{{ $lab->idcliente }}" {{ old('idcliente') == $lab->idcliente ? 'selected' : '' }}>{{ $lab->nombres }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="idsintoma" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Síntoma <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="idsintoma" name="idsintoma" required class="{{ $selectClass }} @error('idsintoma'){{ $errorClass }}@enderror">
                            @foreach($sintomas as $s)
                                <option value="{{ $s->idsintoma }}" {{ old('idsintoma') == $s->idsintoma ? 'selected' : '' }}>{{ $s->sintoma }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="estado" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Estado <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="estado" name="estado" required class="{{ $selectClass }} @error('estado'){{ $errorClass }}@enderror">
                            <option value="1" {{ old('estado', '1') === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="idtipoaf" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo afectación <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="idtipoaf" name="idtipoaf" required class="{{ $selectClass }} @error('idtipoaf'){{ $errorClass }}@enderror">
                            @foreach($tiposAfectacion as $ta)
                                <option value="{{ $ta->idtipoa }}" {{ old('idtipoaf') == $ta->idtipoa ? 'selected' : '' }}>{{ $ta->descripcion }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
                <div>
                    <label for="tipo_precio" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipo precio <span class="text-red-500">*</span></label>
                    <div class="relative z-20 bg-transparent">
                        <select id="tipo_precio" name="tipo_precio" required class="{{ $selectClass }} @error('tipo_precio'){{ $errorClass }}@enderror">
                            <option value="01" {{ old('tipo_precio', '01') === '01' ? 'selected' : '' }}>Trabaja con IGV</option>
                            <option value="02" {{ old('tipo_precio') === '02' ? 'selected' : '' }}>Valor referencial unitario</option>
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <x-ui.button-loader type="submit" label="Registrar" loading-text="Guardando..." class="!px-5 !py-3.5 bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 rounded-lg font-medium text-sm" />
                <a href="{{ route('mantenimiento.productos.index') }}"><x-ui.button type="button" variant="outline" size="md">Cancelar</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('idlote');
    var txtfv = document.getElementById('fecha_vencimiento');
    if (sel && txtfv) {
        sel.addEventListener('change', function() {
            var opt = this.options[this.selectedIndex];
            var fecha = opt.getAttribute('data-fecha');
            if (fecha) txtfv.value = fecha;
        });
    }
});
</script>
@endpush
@endsection
