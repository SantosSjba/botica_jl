@extends('layouts.app')

@php
    $inputClass = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
    $inputReadonlyClass = 'shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800/50 dark:text-white/90';
    $labelClass = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400';
    $simboloMoneda = \App\Models\Configuracion::first()?->simbolo_moneda ?? 'S/';
@endphp

@section('content')
<div class="min-w-0 space-y-6" x-data="notaCreditoForm()">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card title="Nota de crédito" desc="Emita una nota de crédito referenciando una factura o boleta. El comprobante de referencia quedará anulado.">
        <form @submit.prevent="submitForm" class="space-y-6">
            @csrf
            <input type="hidden" name="idventa" x-model="idventa" />
            <input type="hidden" name="serie_ref" x-model="serieRef" />
            <input type="hidden" name="correlativo_ref" x-model="correlativoRef" />

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Tipo de comprobante</label>
                    <input type="text" value="NOTA DE CRÉDITO" readonly class="{{ $inputReadonlyClass }}" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">Motivo</label>
                    <input type="text" value="ANULACIÓN DE LA OPERACIÓN" readonly class="{{ $inputReadonlyClass }}" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="fecha" class="{{ $labelClass }}">Fecha de emisión <span class="text-red-500">*</span></label>
                    <input type="date" id="fecha" name="fecha_emision" required
                        :value="fechaHoy"
                        class="{{ $inputClass }}" />
                </div>
            </div>

            <div>
                <label class="{{ $labelClass }}">Serie y correlativo del comprobante de referencia <span class="text-red-500">*</span></label>
                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">Indique la serie y número del comprobante (factura o boleta) al que aplica la nota de crédito.</p>
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <input type="text" x-model="serieRefInput" placeholder="Ej. F001" maxlength="10"
                            class="{{ $inputClass }} w-28" />
                    </div>
                    <div>
                        <input type="text" x-model="correlativoRefInput" placeholder="Correlativo" maxlength="20"
                            class="{{ $inputClass }} w-32" />
                    </div>
                    <button type="button" @click="buscarReferencia" :disabled="buscarLoading"
                        class="inline-flex h-11 items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-70">
                        <span x-show="!buscarLoading">Buscar</span>
                        <span x-show="buscarLoading" x-cloak class="inline-flex items-center gap-2">
                            <svg class="size-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Buscando...
                        </span>
                    </button>
                </div>
                <p x-show="refError" x-cloak x-text="refError" class="mt-2 text-sm text-red-600 dark:text-red-400"></p>
                <div x-show="refData" x-cloak class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">Comprobante encontrado</p>
                    <p class="text-theme-sm text-gray-600 dark:text-gray-400">Cliente: <span x-text="refData ? refData.cliente : ''"></span></p>
                    <p class="text-theme-sm text-gray-600 dark:text-gray-400">Total: <span x-text="refData ? '{{ $simboloMoneda }} ' + refData.total : ''"></span></p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="serie_n" class="{{ $labelClass }}">Serie de la nota <span class="text-red-500">*</span></label>
                    <x-form.select-wrapper id="serie_n" name="serie_n" x-model="serieN" @change="obtenerCorrelativo">
                        <option value="">Seleccione</option>
                        <option value="BN01">BN01 (Boleta)</option>
                        <option value="FN01">FN01 (Factura)</option>
                    </x-form.select-wrapper>
                </div>
                <div>
                    <label for="correlativo_n" class="{{ $labelClass }}">Correlativo de la nota <span class="text-red-500">*</span></label>
                    <input type="number" id="correlativo_n" name="correlativo_n" min="1" required readonly
                        x-model="correlativoN"
                        class="{{ $inputReadonlyClass }}" />
                </div>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-3.5 text-sm font-medium text-white shadow-theme-xs transition hover:bg-brand-600 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="!idventa || submitLoading">
                    <span x-show="!submitLoading">Registrar nota de crédito</span>
                    <span x-show="submitLoading" x-cloak>Registrando...</span>
                </button>
                <a href="{{ route('ventas.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Cancelar</a>
            </div>
        </form>
    </x-common.component-card>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', function() {
    Alpine.data('notaCreditoForm', function() {
        return {
            idventa: '',
            serieRef: '',
            correlativoRef: '',
            serieRefInput: '',
            correlativoRefInput: '',
            serieN: '',
            correlativoN: '',
            refData: null,
            refError: '',
            buscarLoading: false,
            submitLoading: false,
            fechaHoy: new Date().toLocaleDateString('en-CA', { timeZone: 'America/Lima' }),
            routes: {
                buscar: @json(route('notacredito.buscar')),
                correlativo: @json(route('notacredito.correlativo')),
                store: @json(route('notacredito.store')),
            },
            token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            async buscarReferencia() {
                this.refError = '';
                this.refData = null;
                this.idventa = '';
                this.serieRef = '';
                this.correlativoRef = '';
                var s = (this.serieRefInput || '').trim();
                var c = (this.correlativoRefInput || '').trim();
                if (!s || !c) { this.refError = 'Indique serie y correlativo de referencia.'; return; }
                this.buscarLoading = true;
                try {
                    var res = await window.axios.post(this.routes.buscar, { serie_ref: s, correlativo_ref: c });
                    var data = res.data;
                    if (data.success) {
                        this.idventa = data.idventa;
                        this.serieRef = data.serie_ref;
                        this.correlativoRef = data.correlativo_ref;
                        this.refData = data;
                        this.obtenerCorrelativo();
                    } else {
                        this.refError = data.message || 'No encontrado.';
                    }
                } finally {
                    this.buscarLoading = false;
                }
            },
            async obtenerCorrelativo() {
                if (!this.serieN) { this.correlativoN = ''; return; }
                var res = await window.axios.post(this.routes.correlativo, { serie_n: this.serieN });
                var data = res.data;
                if (data.success && data.correlativo) this.correlativoN = data.correlativo;
            },
            async submitForm() {
                if (!this.idventa) return;
                var form = document.querySelector('form');
                var fd = new FormData(form);
                fd.set('idventa', this.idventa);
                fd.set('serie_ref', this.serieRef);
                fd.set('correlativo_ref', this.correlativoRef);
                fd.set('correlativo_n', this.correlativoN);
                this.submitLoading = true;
                try {
                    var res = await window.axios.post(this.routes.store, fd, { headers: { 'Accept': 'application/json' } });
                    var data = res.data;
                    if (data.success) {
                        if (typeof window.showToast === 'function') window.showToast(data.message || 'Nota de crédito registrada.', 'success');
                        else alert(data.message);
                        window.location.href = @json(route('notacredito.index'));
                    } else {
                        if (typeof window.showToast === 'function') window.showToast(data.message || 'Error al registrar.', 'error');
                        else alert(data.message || 'Error al registrar.');
                    }
                } finally {
                    this.submitLoading = false;
                }
            }
        };
    });
});
</script>
@endpush
@endsection
