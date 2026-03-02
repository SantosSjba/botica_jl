@extends('layouts.app')

@section('content')
<div class="min-w-0 space-y-6">
    <x-common.page-breadcrumb :pageTitle="$title" />

    <x-common.component-card
        title="Copia de respaldo de la base de datos"
        desc="Genere y descargue un archivo SQL con el volcado completo de la base de datos configurada en el sistema. Use esta copia para restaurar en otro servidor o como respaldo periódico."
    >
        <div class="space-y-6">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800/50">
                <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:justify-center sm:text-left">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-brand-100 dark:bg-brand-500/20">
                        <svg class="size-8 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Descargar respaldo SQL</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Se generará un archivo .sql con la estructura y datos de todas las tablas. La descarga puede tardar unos segundos según el tamaño de la base de datos.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 border-t border-gray-100 pt-4 dark:border-gray-800">
                <a href="{{ route('backup.download') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-brand-600 focus:ring-3 focus:ring-brand-500/20 focus:outline-hidden dark:bg-brand-600 dark:hover:bg-brand-700">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Descargar copia de respaldo
                </a>
            </div>
        </div>
    </x-common.component-card>
</div>
@endsection
