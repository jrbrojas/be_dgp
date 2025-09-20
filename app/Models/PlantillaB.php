<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaB extends Model
{
    protected $table = 'plantilla_b';

    protected $fillable = [
        'escenario_id',
        'tipo',
        'ubigeo',
        'viviendas',
        'poblacion',
        'red_agua',
        'reservorios',
        'ptar',
        'ptap',
        'grupos_vulnerables',
        'material_pared_predominante',
        'red_vial_nacional',
        'red_vial_departamental',
        'red_vial_vecinal',
        'puentes',
        'red_ferroviaria',
        'aerodromos',
        'puertos',
        'locales_educativos',
        'poblacion_indigena',
        'bienes_inmuebles',
        'patrimonio_historico',
        'museos',
        'es_cr',
        'es',
        'vulnerabilidad',
        'nivel_peligro_sismo',
        'nivel_peligro_tsunami',
        'nivel_peligro_glaciar',
        'nivel_peligro_movimientos_masa',
        'valor_riesgo_sismo',
        'valor_riesgo_tsunami',
        'valor_riesgo_glaciar',
        'valor_riesgo_movimientos_masa',
        'nr_sismo',
        'nr_tsunami',
        'nr_glaciar',
        'nr_movimientos_masa',
    ];

    protected $casts = [
        'red_vial_nacional' => 'decimal:2',
        'red_vial_departamental' => 'decimal:2',
        'red_vial_vecinal' => 'decimal:2',
        'red_ferroviaria' => 'decimal:2',
        'valor_riesgo_sismo' => 'decimal:2',
        'valor_riesgo_tsunami' => 'decimal:2',
        'valor_riesgo_glaciar' => 'decimal:2',
        'valor_riesgo_movimientos_masa' => 'decimal:2',
    ];
}
