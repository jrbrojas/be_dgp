<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlantillaB extends Model
{
    protected $table = 'plantilla_b';

    protected $fillable = [
        'escenario_id',
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

    // formulario 9
    public static function getByFormularioSismosTsunamiNacional(Escenario $escenario)
    {
        $sismos = DB::table('plantilla_b as pla')
            ->leftJoin('distritos as dr', 'pla.ubigeo', '=', 'dr.codigo')
            ->leftJoin('provincias as pr', 'dr.provincia_id', '=', 'pr.id')
            ->leftJoin('departamentos as d', 'pr.departamento_id', '=', 'd.id')
            ->where('pla.escenario_id', $escenario->id)
            ->whereNotNull('pla.nr_sismo')
            ->selectRaw("
                pla.nr_sismo AS nivel,
                SUM(pla.poblacion) AS total_poblacion,
                SUM(pla.viviendas) AS total_vivienda,
                COUNT(DISTINCT pla.ubigeo) AS total_distritos,
                SUM(pla.locales_educativos) AS total_inst_educativa,
                SUM(pla.es) AS total_est_salud,
                SUM(pla.red_vial_nacional) AS total_red_vial_nacional,
                SUM(pla.red_vial_departamental) AS total_red_vial_departamental,
                SUM(pla.red_vial_vecinal) AS total_red_vial_vecinal,
                SUM(pla.red_agua) AS total_red_agua
            ")
            ->groupBy('pla.nr_sismo')
            ->orderByRaw("
                CASE UPPER(pla.nr_sismo)
                    WHEN 'MUY ALTO' THEN 1
                    WHEN 'ALTO' THEN 2
                    WHEN 'MEDIO' THEN 3
                    WHEN 'BAJO' THEN 4
                    WHEN 'MUY BAJO' THEN 5
                    ELSE 6
                END
            ")
            ->get();

        $tsunamis = DB::table('plantilla_b as pla')
            ->leftJoin('distritos as dr', 'pla.ubigeo', '=', 'dr.codigo')
            ->leftJoin('provincias as pr', 'dr.provincia_id', '=', 'pr.id')
            ->leftJoin('departamentos as d', 'pr.departamento_id', '=', 'd.id')
            ->where('pla.escenario_id', $escenario->id)
            ->whereNotNull('pla.nr_tsunami')
            ->selectRaw("
                pla.nr_tsunami AS nivel,
                SUM(pla.poblacion) AS total_poblacion,
                SUM(pla.viviendas) AS total_vivienda,
                COUNT(DISTINCT pla.ubigeo) AS total_distritos,
                SUM(pla.locales_educativos) AS total_inst_educativa,
                SUM(pla.es) AS total_est_salud,
                SUM(pla.red_vial_nacional) AS total_red_vial_nacional,
                SUM(pla.red_vial_departamental) AS total_red_vial_departamental,
                SUM(pla.red_vial_vecinal) AS total_red_vial_vecinal,
                SUM(pla.red_agua) AS total_red_agua
            ")
            ->groupBy('pla.nr_tsunami')
            ->orderByRaw("
                CASE UPPER(pla.nr_tsunami)
                    WHEN 'MUY ALTO' THEN 1
                    WHEN 'ALTO' THEN 2
                    WHEN 'MEDIO' THEN 3
                    WHEN 'BAJO' THEN 4
                    WHEN 'MUY BAJO' THEN 5
                    ELSE 6
                END
            ")
            ->get();

        $glaciares = DB::table('plantilla_b as pla')
            ->leftJoin('distritos as dr', 'pla.ubigeo', '=', 'dr.codigo')
            ->leftJoin('provincias as pr', 'dr.provincia_id', '=', 'pr.id')
            ->leftJoin('departamentos as d', 'pr.departamento_id', '=', 'd.id')
            ->where('pla.escenario_id', $escenario->id)
            ->whereNotNull('pla.nr_glaciar')
            ->selectRaw("
                pla.nr_glaciar AS nivel,
                SUM(pla.poblacion) AS total_poblacion,
                SUM(pla.viviendas) AS total_vivienda,
                COUNT(DISTINCT pla.ubigeo) AS total_distritos,
                SUM(pla.locales_educativos) AS total_inst_educativa,
                SUM(pla.es) AS total_est_salud,
                SUM(pla.red_vial_nacional) AS total_red_vial_nacional,
                SUM(pla.red_vial_departamental) AS total_red_vial_departamental,
                SUM(pla.red_vial_vecinal) AS total_red_vial_vecinal,
                SUM(pla.red_agua) AS total_red_agua
            ")
            ->groupBy('pla.nr_glaciar')
            ->orderByRaw("
                CASE UPPER(pla.nr_glaciar)
                    WHEN 'MUY ALTO' THEN 1
                    WHEN 'ALTO' THEN 2
                    WHEN 'MEDIO' THEN 3
                    WHEN 'BAJO' THEN 4
                    WHEN 'MUY BAJO' THEN 5
                    ELSE 6
                END
            ")
            ->get();

        $movimiento_masa = DB::table('plantilla_b as pla')
            ->leftJoin('distritos as dr', 'pla.ubigeo', '=', 'dr.codigo')
            ->leftJoin('provincias as pr', 'dr.provincia_id', '=', 'pr.id')
            ->leftJoin('departamentos as d', 'pr.departamento_id', '=', 'd.id')
            ->where('pla.escenario_id', $escenario->id)
            ->whereNotNull('pla.nr_movimientos_masa')
            ->selectRaw("
                pla.nr_movimientos_masa AS nivel,
                SUM(pla.poblacion) AS total_poblacion,
                SUM(pla.viviendas) AS total_vivienda,
                COUNT(DISTINCT pla.ubigeo) AS total_distritos,
                SUM(pla.locales_educativos) AS total_inst_educativa,
                SUM(pla.es) AS total_est_salud,
                SUM(pla.red_vial_nacional) AS total_red_vial_nacional,
                SUM(pla.red_vial_departamental) AS total_red_vial_departamental,
                SUM(pla.red_vial_vecinal) AS total_red_vial_vecinal,
                SUM(pla.red_agua) AS total_red_agua
            ")
            ->groupBy('pla.nr_movimientos_masa')
            ->orderByRaw("
                CASE UPPER(pla.nr_movimientos_masa)
                    WHEN 'MUY ALTO' THEN 1
                    WHEN 'ALTO' THEN 2
                    WHEN 'MEDIO' THEN 3
                    WHEN 'BAJO' THEN 4
                    WHEN 'MUY BAJO' THEN 5
                    ELSE 6
                END
            ")
            ->get();

        return [
            'sismos' => $sismos,
            'tsunamis' => $tsunamis,
            'glaciares' => $glaciares,
            'movimiento_masa' => $movimiento_masa,
        ];
    }

}
