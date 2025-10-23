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

        $clean = preg_replace(['/^{|}$/', '/"|\'/'], '', $this->pgArray);

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
