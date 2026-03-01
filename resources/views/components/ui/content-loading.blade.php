{{--
    Wrapper reutilizable para contenido que se actualiza por AJAX.
    Props: wrapperId, loadingId, contentContainerId, loadingText (opcional).
    En JS: antes del fetch hacer document.getElementById(loadingId).classList.remove('hidden'); classList.add('flex');
    después del fetch: classList.add('hidden'); classList.remove('flex');
    Reemplazar contenido: document.getElementById(contentContainerId).innerHTML = html;
--}}
<div id="{{ $wrapperId }}" class="relative">
    <div id="{{ $loadingId }}"
         class="absolute inset-0 z-10 hidden items-center justify-center rounded-b-2xl bg-white/80 dark:bg-gray-900/80"
         aria-hidden="true">
        <div class="flex flex-col items-center gap-2">
            <svg class="size-10 animate-spin text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $loadingText }}</span>
        </div>
    </div>
    <div id="{{ $contentContainerId }}" @if($contentContainerClass) class="{{ $contentContainerClass }}" @endif>
        {{ $slot }}
    </div>
</div>
