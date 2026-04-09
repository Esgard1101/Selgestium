<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Autocomplete extends Component
{
    public int $minChars;
    public string $uid;

    public function __construct(
        public string $name,
        public string $label,
        public string $endpoint,
        public string $placeholder = '',
        public string|int $value = '',
        public string $valueLabel = '',
        public bool $required = false,
    ) {
        $this->minChars = (int) config('activos.min_chars_autocomplete', 2);
        // UID único por instancia para evitar colisiones si hay varios en la misma página
        $this->uid = 'ac_' . preg_replace('/[^a-z0-9]/i', '_', $name) . '_' . uniqid();
    }

    public function render(): View|Closure|string
    {
        return view('components.autocomplete');
    }
}
