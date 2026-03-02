@props([
    'title',
    'desc' => '',
    'compact' => false,
])

@php
    $headerClass = $compact ? 'px-4 py-3' : 'px-6 py-5';
    $bodyClass = $compact ? 'p-3 border-t border-gray-100 dark:border-gray-800' : 'p-4 border-t border-gray-100 dark:border-gray-800 sm:p-6';
    $bodySpace = $compact ? 'space-y-4' : 'space-y-6';
    $titleSize = $compact ? 'text-sm font-medium' : 'text-base font-medium';
@endphp
<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]']) }}>
    <!-- Card Header -->
    <div class="{{ $headerClass }}">
        <h3 class="{{ $titleSize }} text-gray-800 dark:text-white/90">
            {{ $title }}
        </h3>
        @if($desc)
            <p class="{{ $compact ? 'mt-0.5 text-xs' : 'mt-1 text-sm' }} text-gray-500 dark:text-gray-400">
                {{ $desc }}
            </p>
        @endif
    </div>

    <!-- Card Body -->
    <div class="{{ $bodyClass }}">
        <div class="{{ $bodySpace }}">
            {{ $slot }}
        </div>
    </div>
</div>