<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormatNombreArray extends Component
{

    public ?string $pgArray;
    /**
     * Create a new component instance.
     */
    public function __construct(?string $pgArray = null)
    {
        $this->pgArray = $pgArray;
    }

        /**
     * Procesa y limpia el array de Postgres.
     */
    public function getItems(): array
    {
        if (empty($this->pgArray)) {
            return [];
        }

        $decoded = html_entity_decode($this->pgArray, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = preg_replace(['/^{|}$/', '/["\']+/'], '', $decoded);

        return array_filter(
            array_map('trim', explode(',', $clean)),
            fn($v) => $v !== '' && strtoupper($v) !== 'NULL'
        );
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.format-nombre-array');
    }
}
