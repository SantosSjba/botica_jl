<?php

namespace App\View\Components\ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Wrapper reutilizable para contenido que se actualiza por AJAX.
 * Muestra un overlay con spinner y texto "Cargando..." mientras se hace fetch.
 * Uso en JS: getElementById($loadingId) y alternar 'hidden'/'flex'; reemplazar contenido de $contentContainerId.
 */
class ContentLoading extends Component
{
    public function __construct(
        public string $wrapperId,
        public string $loadingId,
        public string $contentContainerId,
        public string $loadingText = 'Cargando...',
        public string $contentContainerClass = '',
    ) {
    }

    public function render(): View|Closure|string
    {
        return view('components.ui.content-loading');
    }
}
