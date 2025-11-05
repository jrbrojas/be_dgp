<?php

namespace App\View\Components;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FechaRango extends Component
{

    public ?string $fechaInicio;
    public ?string $fechaFin;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $fechaInicio = null, ?string $fechaFin = null)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function texto(): string
    {
        if (empty($this->fechaInicio) || empty($this->fechaFin)) {
            return '';
        }

        Carbon::setLocale('es');

        $inicio = Carbon::parse($this->fechaInicio);
        $fin = Carbon::parse($this->fechaFin);

        $diaInicio = $inicio->day;
        $diaFin = $fin->day;
        $mesInicio = $inicio->translatedFormat('F');
        $mesFin = $fin->translatedFormat('F');
        $anioInicio = $inicio->year;
        $anioFin = $fin->year;

        $cap = fn($str) => ucfirst($str);

        if ($anioInicio === $anioFin && $mesInicio === $mesFin) {
            // 🟢 mismo mes y año
            return "del {$diaInicio} al {$diaFin} de {$cap($mesFin)} del {$anioFin}";
        } elseif ($anioInicio === $anioFin) {
            // 🟠 diferente mes, mismo año
            return "del {$diaInicio} de {$cap($mesInicio)} al {$diaFin} de {$cap($mesFin)} del {$anioFin}";
        } else {
            // 🔴 diferente año
            return "del {$diaInicio} de {$cap($mesInicio)} del {$anioInicio} al {$diaFin} de {$cap($mesFin)} del {$anioFin}";
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.fecha-rango');
    }
}
