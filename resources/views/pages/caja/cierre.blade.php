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

    @if (session('error'))
        <x-ui.alert variant="error" :message="session('error')" />
    @endif

    <x-common.component-card title="Cierre de caja" desc="Verifique los totales e ingrese el efectivo físico en caja para cerrar.">
        <form action="{{ route('caja.store-cierre') }}" method="post" x-data="{ loading: false }" @submit="loading = true" class="space-y-6" id="form-cierre-caja">
            @csrf
            <input type="hidden" name="idcaja_a" value="{{ $cajaApertura->idcaja_a }}" />
            <input type="hidden" name="txtfec" value="{{ $fechaCaja }}" />
            <input type="hidden" name="txtusu" value="{{ $usuario }}" />
            <input type="hidden" name="txtturno" value="{{ $cajaApertura->turno }}" />
            <input type="hidden" name="txthor" value="{{ $hora }}" />

            <div>
                <label class="{{ $labelClass }}">Cajero</label>
                <div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800/50">
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $usuario }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Fecha</label>
                    <input type="text" value="{{ $fechaCaja }}" readonly class="{{ $inputReadonlyClass }}" />
                    @if($fechaCaja !== $diaActual)
                        <p class="mt-1 text-sm text-amber-600 dark:text-amber-400"><strong>Nota:</strong> Esta caja fue abierta el {{ $fechaCaja }}.</p>
                    @endif
                </div>
                <div>
                    <label class="{{ $labelClass }}">Caja</label>
                    <input type="text" name="txtcaja" value="{{ $cajaApertura->caja }}" readonly class="{{ $inputReadonlyClass }}" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                <div>
                    <label class="{{ $labelClass }}">Total pagos en efectivo</label>
                    <input type="text" name="txtp_e" id="txtp_e" value="{{ number_format($formaEfectivo, 2) }}" readonly class="{{ $inputReadonlyClass }}" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">Total pagos con tarjeta</label>
                    <input type="text" name="txt_t" id="txt_t" value="{{ number_format($formaTarjeta, 2) }}" readonly class="{{ $inputReadonlyClass }}" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">Total depósitos bancarios</label>
                    <input type="text" name="txtp_d" id="txtp_d" value="{{ number_format($formaDeposito, 2) }}" readonly class="{{ $inputReadonlyClass }}" />
                </div>
            </div>

            @php
                $otrasFormas = ['YAPE', 'PLIN', 'TRANSFERENCIA', 'OTRO'];
            @endphp
            @foreach($otrasFormas as $fm)
                @if(!empty($porForma[$fm]))
                    <div class="max-w-xs">
                        <label class="{{ $labelClass }}">Total {{ $fm }}</label>
                        <input type="text" value="{{ number_format($porForma[$fm], 2) }}" readonly class="{{ $inputReadonlyClass }}" />
                    </div>
                @endif
            @endforeach

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Total de ventas</label>
                    <input type="text" name="txttot" id="txttot" value="{{ number_format($totalVentas, 2) }}" readonly class="{{ $inputReadonlyClass }}" />
                </div>
                <div>
                    <label class="{{ $labelClass }}">Monto de apertura</label>
                    <input type="text" name="txtmon" id="txtmon" value="{{ number_format((float)$cajaApertura->monto, 2) }}" readonly class="{{ $inputReadonlyClass }}" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="{{ $labelClass }}">Total en caja (sistema)</label>
                    <input type="text" name="txtsis" id="txtsis" value="{{ number_format($cajaSistema, 2) }}" readonly class="{{ $inputReadonlyClass }}" />
                </div>
                <div>
                    <label for="txtefe" class="{{ $labelClass }}">Efectivo en caja (físico) <span class="text-red-500">*</span></label>
                    <input type="number" name="txtefe" id="txtefe" value="{{ old('txtefe') }}" min="0" step="0.01" required
                        placeholder="Ingrese el monto que se encuentra en la caja física"
                        class="{{ $inputClass }} @error('txtefe'){{ $errorClass }}@enderror" />
                </div>
            </div>

            <div class="max-w-xs">
                <label class="{{ $labelClass }}">Diferencia (faltante/sobrante)</label>
                <input type="text" name="txtfalta" id="txtfalta" readonly class="{{ $inputReadonlyClass }}" />
            </div>

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <x-ui.button-loader type="submit" label="Cerrar caja" loading-text="Guardando..." class="!px-5 !py-3.5 bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:opacity-50 rounded-lg font-medium text-sm" />
                <a href="{{ route('caja.seguimiento') }}"><x-ui.button type="button" variant="outline" size="md">Cancelar</x-ui.button></a>
            </div>
        </form>
    </x-common.component-card>
</div>
@push('scripts')
<script>
(function() {
    var sistema = document.getElementById('txtsis');
    var efectivo = document.getElementById('txtefe');
    var falta = document.getElementById('txtfalta');
    if (!efectivo || !falta) return;
    function actualizarDiferencia() {
        var s = parseFloat(sistema.value.replace(/,/g, '')) || 0;
        var e = parseFloat(efectivo.value) || 0;
        falta.value = (s - e).toFixed(2);
    }
    efectivo.addEventListener('input', actualizarDiferencia);
    efectivo.addEventListener('blur', actualizarDiferencia);
})();
</script>
@endpush
@endsection
