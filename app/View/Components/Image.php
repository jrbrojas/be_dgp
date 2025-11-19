<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Image extends Component
{
    public string $src;
    public string $alt;
    public ?string $caption;
    public string $fit;
    public ?string $ratio;
    public bool $border;
    public bool $shadow;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $src = null,
        string $alt = '',
        ?string $caption = null,
        string $fit = 'cover',
        ?string $ratio = null,
        bool $border = false,
        bool $shadow = false,
    )
    {
        $this->src = $src;
        $this->alt = $alt;
        $this->caption = $caption;
        $this->fit = in_array($fit, ['cover', 'contain']) ? $fit : 'cover';
        $this->ratio = $ratio;
        $this->border = $border;
        $this->shadow = $shadow;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.image');
    }
}
