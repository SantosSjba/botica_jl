<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full min-w-[640px] text-left text-sm text-gray-700 dark:text-gray-300">
            <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">#</th>
                    <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Fecha</th>
                    <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Hora apert.</th>
                    @if($esAdministrador)
                        <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Cajero</th>
                    @endif
                    <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Caja</th>
                    <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Turno</th>
                    <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Monto apert.</th>
                    <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Estado</th>
                    <th class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aperturas as $index => $row)
                    @php
                        $urlCuadre = route('reportes.cuadrecaja', ['usuario' => $row->usuario, 'fecha' => $row->fecha->format('Y-m-d')]);
                    @endphp
                    <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50/50 dark:hover:bg-gray-800/30">
                        <td class="px-4 py-3">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">{{ $row->fecha->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">{{ $row->hora }}</td>
                        @if($esAdministrador)
                            <td class="px-4 py-3">{{ $row->usuario }}</td>
                        @endif
                        <td class="px-4 py-3">{{ $row->caja }}</td>
                        <td class="px-4 py-3">{{ $row->turno }}</td>
                        <td class="px-4 py-3">{{ number_format((float)$row->monto, 2) }}</td>
                        <td class="px-4 py-3">
                            @if($row->estado === 'Abierto')
                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">Abierto</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cerrado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ $urlCuadre }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Cuadre
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $esAdministrador ? 9 : 8 }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hay registros de caja para la fecha seleccionada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
