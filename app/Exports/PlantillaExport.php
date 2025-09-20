<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PlantillaExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($item) {
            return [
                'Centros poblados' => $item['total_inu_cp'] ?? '',
                'Población' => $item['total_poblacion'] ?? '',
                'Viviendas' => $item['total_vivienda'] ?? '',
                'Inst. Educativas' => $item['total_inu_ie'] ?? '',
                'Est. de Salud' => $item['total_inu_es'] ?? '',
                'Nivel' => $item['nivel'] ?? '',
            ];
        });
    }

    public function headings(): array
    {
            return ['CENTROS POBLADOS', 'POBLACION', 'VIVIENDAS', 'INST. EDUCATIVAS', 'EST. DE SALUD', 'NIVEL'];
    }

}
