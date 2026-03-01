<?php

namespace App\View\Components\ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ButtonLoader extends Component
{
    public function __construct(
        public string $label = 'Enviar',
        public string $loadingText = 'Cargando...',
        public string $type = 'submit',
        public string $class = '',
        public string $variant = 'primary',
    ) {
        $this->class = trim('inline-flex items-center justify-center gap-2 ' . $class);
    }

    public function render(): View|Closure|string
    {
        return view('components.ui.button-loader');
    }
}
