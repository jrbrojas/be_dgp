<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Staudenmeir\LaravelCte\Query\Builder;

class PlantillaA extends Model
{
    protected $table = 'plantilla_a';
    protected $fillable = [
        'escenario_id',
        'tipo',
        'cod_cp',
        'cod_ubigeo',
        'poblacion',
        'vivienda',
        'ie',
        'es',
        'nivel_riesgo',
        'nivel_riesgo_agricola',
        'nivel_riesgo_pecuario',
        'cantidad_cp',
        'nivel_susceptibilidad',
        'nivel_exposicion_1_mm',
        'nivel_exposicion_2_inu',
        'nivel_exposicion_3_bt',
        'alumnos',
        'docentes',
        'vias',
        'superficie_agricola',
        'pob_5',
        'pob_60',
        'pob_urb',
        'pob_rural',
        'viv_tipo1',
        'viv_tipo2',
        'viv_tipo3',
        'viv_tipo4',
        'viv_tipo5',
        'hogares',
        'sa_riego',
        'sa_secano',
        'prod_agropecuarios',
        'prod_agropecuarios_65',
        'superficie_de_pastos',
        'alpacas',
        'ovinos',
        'vacunos',
        'areas_naturales',
        'nivel_sequia',
    ];

    protected $casts = [
        'vias' => 'decimal:8,2',
        'superficie_agricola' => 'decimal:8,2',
        'sa_riego' => 'decimal:8,2',
        'sa_secano' => 'decimal:8,2',
        'prod_agropecuarios_65' => 'decimal:8,2',
        'superficie_de_pastos' => 'decimal:8,2',
    ];

    public static function getByFormularioAvisoMeteorologico(Escenario $escenario)
    {
        $inundaciones = DB::table('plantilla_a as pla')
            ->leftJoin('centro_poblados as cp', function ($join) {
                $join->on('pla.cod_cp', '=', 'cp.codigo')
                    ->where('pla.tipo', '=', 'INU_CP');
            })
            ->leftJoin('distritos as dr_cp', 'cp.distrito_id', '=', 'dr_cp.id') // ruta desde centro poblado
            ->leftJoin('distritos as dr_alt', function ($join) {
                $join->on('pla.cod_ubigeo', '=', 'dr_alt.codigo')
                    ->where('pla.tipo', '<>', 'INU_CP');
            })
            ->leftJoin('provincias as pr_cp', 'dr_cp.provincia_id', '=', 'pr_cp.id')
            ->leftJoin('provincias as pr_alt', 'dr_alt.provincia_id', '=', 'pr_alt.id')
            ->leftJoin('departamentos as d_cp', 'pr_cp.departamento_id', '=', 'd_cp.id')
            ->leftJoin('departamentos as d_alt', 'pr_alt.departamento_id', '=', 'd_alt.id')
            ->where('pla.escenario_id', $escenario->id)
            ->selectRaw("
                pla.nivel_exposicion_2_inu AS nivel,
                SUM(CASE WHEN pla.tipo = 'INU_CP' THEN pla.poblacion ELSE 0 END) AS total_poblacion,
                SUM(CASE WHEN pla.tipo = 'INU_CP' THEN pla.vivienda ELSE 0 END) AS total_vivienda,
                COUNT(CASE WHEN pla.tipo = 'INU_CP' THEN 1 END) AS total_centro_poblado,
                COUNT(CASE WHEN pla.tipo = 'INU_ES' THEN 1 END) AS total_est_salud,
                COUNT(CASE WHEN pla.tipo = 'INU_IE' THEN 1 END) AS total_inst_educativa,
                ARRAY_AGG(DISTINCT COALESCE(d_cp.nombre, d_alt.nombre)) AS departamentos
            ")
            ->whereNotNull('pla.nivel_exposicion_2_inu')
            ->groupBy('pla.nivel_exposicion_2_inu')
            ->orderByRaw("
                CASE UPPER(pla.nivel_exposicion_2_inu)
                    WHEN 'MUY ALTO' THEN 1
                    WHEN 'ALTO' THEN 2
                    WHEN 'MEDIO' THEN 3
                    WHEN 'BAJO' THEN 4
                    WHEN 'MUY BAJO' THEN 5
                    ELSE 6
                END
            ")
            ->get();

        // $inundaciones = DB::table('plantilla_a as pla')
        //     ->withExpression('base', function ($query) use($escenario) {
        //         $query->selectRaw("
        //                 pla.id,
        //                 pla.escenario_id,
        //                 pla.nivel_exposicion_2_inu AS nivel,
        //                 pla.tipo,
        //                 pla.poblacion,
        //                 pla.vivienda,
        //                 COALESCE(d_cp.nombre, d_alt.nombre) AS departamento
        //             ")
        //             ->from('plantilla_a as pla')
        //             ->leftJoin('centro_poblados as cp', function ($join) {
        //                 $join->on('pla.cod_cp', '=', 'cp.codigo')
        //                     ->where('pla.tipo', '=', 'INU_CP');
        //             })
        //             ->leftJoin('distritos as dr_cp', 'cp.distrito_id', '=', 'dr_cp.id')
        //             ->leftJoin('distritos as dr_alt', function ($join) {
        //                 $join->on('pla.cod_ubigeo', '=', 'dr_alt.codigo')
        //                     ->where('pla.tipo', '<>', 'INU_CP');
        //             })
        //             ->leftJoin('provincias as pr_cp', 'dr_cp.provincia_id', '=', 'pr_cp.id')
        //             ->leftJoin('provincias as pr_alt', 'dr_alt.provincia_id', '=', 'pr_alt.id')
        //             ->leftJoin('departamentos as d_cp', 'pr_cp.departamento_id', '=', 'd_cp.id')
        //             ->leftJoin('departamentos as d_alt', 'pr_alt.departamento_id', '=', 'd_alt.id')
        //             ->where('pla.escenario_id', $escenario->id)
        //             ->whereNotNull('pla.nivel_exposicion_2_inu');
        //     })
        //     ->get();

        $movimiento_masa = DB::table('plantilla_a as pla')
            ->leftJoin('distritos as dr', 'pla.cod_ubigeo', '=', 'dr.codigo')
            ->leftJoin('provincias as pr', 'dr.provincia_id', '=', 'pr.id')
            ->leftJoin('departamentos as d', 'pr.departamento_id', '=', 'd.id')
            ->where('pla.escenario_id', $escenario->id)
            ->where('pla.tipo', 'AM_MM')
            ->selectRaw("
                pla.nivel_riesgo AS nivel,
                SUM(pla.poblacion) AS total_poblacion,
                SUM(pla.vivienda) AS total_vivienda,
                SUM(pla.es) AS total_est_salud,
                SUM(pla.ie) AS total_inst_educativa,
                COUNT(DISTINCT dr.id) AS total_distritos,
                ARRAY_AGG(DISTINCT COALESCE(d.nombre)) AS departamentos
            ")
            ->whereNotNull('pla.nivel_riesgo')
            ->groupBy('pla.nivel_riesgo')
            ->orderByRaw("
                CASE UPPER(pla.nivel_riesgo)
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
            'inundaciones' => $inundaciones,
            'movimiento_masa' => $movimiento_masa,
        ];
    }

    public static function getByFormularioAvisoTrimestral(Escenario $escenario)
    {
        // solo para formulario Lluvias Meteorologico
        $inundaciones = DB::table('plantilla_a as pla')
            ->where('pla.escenario_id', $escenario->id)
            ->selectRaw("
                pla.nivel_exposicion_2_inu AS nivel,
                SUM(CASE WHEN pla.tipo = 'TRI_LLUVIAS_CP' THEN pla.poblacion ELSE 0 END) AS total_poblacion,
                SUM(CASE WHEN pla.tipo = 'TRI_LLUVIAS_CP' THEN pla.vivienda ELSE 0 END) AS total_vivienda,
                COUNT(CASE WHEN pla.tipo = 'TRI_LLUVIAS_CP' THEN 1 END) AS total_centro_poblado,
                COUNT(CASE WHEN pla.tipo = 'TRI_LLUVIAS_ES' THEN 1 END) AS total_est_salud,
                COUNT(CASE WHEN pla.tipo = 'TRI_LLUVIAS_IE' THEN 1 END) AS total_inst_educativa
            ")
            ->whereNotNull('pla.nivel_exposicion_2_inu')
            ->groupBy('pla.nivel_exposicion_2_inu')
            ->orderByRaw("
                CASE UPPER(pla.nivel_exposicion_2_inu)
                    WHEN 'MUY ALTO' THEN 1
                    WHEN 'ALTO' THEN 2
                    WHEN 'MEDIO' THEN 3
                    WHEN 'BAJO' THEN 4
                    WHEN 'MUY BAJO' THEN 5
                    ELSE 6
                END
            ")
            ->get();

        $movimiento_masa = DB::table('plantilla_a as pla')
            ->where('pla.escenario_id', $escenario->id)
            ->selectRaw("
                pla.nivel_exposicion_1_mm AS nivel,
                SUM(CASE WHEN pla.tipo = 'TRI_LLUVIAS_CP' THEN pla.poblacion ELSE 0 END) AS total_poblacion,
                SUM(CASE WHEN pla.tipo = 'TRI_LLUVIAS_CP' THEN pla.vivienda ELSE 0 END) AS total_vivienda,
                COUNT(CASE WHEN pla.tipo = 'TRI_LLUVIAS_CP' THEN 1 END) AS total_centro_poblado,
                COUNT(CASE WHEN pla.tipo = 'TRI_LLUVIAS_ES' THEN 1 END) AS total_est_salud,
                COUNT(CASE WHEN pla.tipo = 'TRI_LLUVIAS_IE' THEN 1 END) AS total_inst_educativa
            ")
            ->whereNotNull('pla.nivel_exposicion_1_mm')
            ->groupBy('pla.nivel_exposicion_1_mm')
            ->orderByRaw("
                CASE UPPER(pla.nivel_exposicion_1_mm)
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
            'inundaciones' => $inundaciones,
            'movimiento_masa' => $movimiento_masa,
        ];
    }

    public static function getByFormularioInformacionClimatica(Escenario $escenario)
    {
        // solo para formulario Lluvias Meteorologico
        $inundaciones = DB::table('plantilla_a as pla')
            ->leftJoin('distritos as dr', 'pla.cod_ubigeo', '=', 'dr.codigo')
            ->leftJoin('provincias as pr', 'dr.provincia_id', '=', 'pr.id')
            ->leftJoin('departamentos as d', 'pr.departamento_id', '=', 'd.id')
            ->where('pla.escenario_id', $escenario->id)
            ->where('pla.tipo', 'CLI_INU')
            ->selectRaw("
                pla.nivel_riesgo AS nivel,
                SUM(pla.poblacion) AS total_poblacion,
                SUM(pla.vivienda) AS total_vivienda,
                SUM(pla.es) AS total_est_salud,
                SUM(pla.ie) AS total_inst_educativa,
                SUM(pla.vias) AS total_vias,
                SUM(pla.superficie_agricola) AS total_superficie_agricola,
                COUNT(DISTINCT dr.id) AS total_distritos
            ")
            ->whereNotNull('pla.nivel_riesgo')
            ->groupBy('pla.nivel_riesgo')
            ->orderByRaw("
                CASE UPPER(pla.nivel_riesgo)
                    WHEN 'MUY ALTO' THEN 1
                    WHEN 'ALTO' THEN 2
                    WHEN 'MEDIO' THEN 3
                    WHEN 'BAJO' THEN 4
                    WHEN 'MUY BAJO' THEN 5
                    ELSE 6
                END
            ")
            ->get();

        $movimiento_masa = DB::table('plantilla_a as pla')
            ->leftJoin('distritos as dr', 'pla.cod_ubigeo', '=', 'dr.codigo')
            ->leftJoin('provincias as pr', 'dr.provincia_id', '=', 'pr.id')
            ->leftJoin('departamentos as d', 'pr.departamento_id', '=', 'd.id')
            ->where('pla.escenario_id', $escenario->id)
            ->where('pla.tipo', 'CLI_MM')
            ->selectRaw("
                pla.nivel_riesgo AS nivel,
                SUM(pla.poblacion) AS total_poblacion,
                SUM(pla.vivienda) AS total_vivienda,
                SUM(pla.es) AS total_est_salud,
                SUM(pla.ie) AS total_inst_educativa,
                SUM(pla.vias) AS total_vias,
                SUM(pla.superficie_agricola) AS total_superficie_agricola,
                COUNT(DISTINCT dr.id) AS total_distritos
            ")
            ->whereNotNull('pla.nivel_riesgo')
            ->groupBy('pla.nivel_riesgo')
            ->orderByRaw("
                CASE UPPER(pla.nivel_riesgo)
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
            'inundaciones' => $inundaciones,
            'movimiento_masa' => $movimiento_masa,
        ];
    }

    public static function getByFormularioBajasTempAvisoMeteorologico(Escenario $escenario)
    {
        // solo para formulario Lluvias Meteorologico
        $inundaciones = DB::table('plantilla_a as pla')
            ->leftJoin('distritos as dr', 'pla.cod_ubigeo', '=', 'dr.codigo')
            ->leftJoin('provincias as pr', 'dr.provincia_id', '=', 'pr.id')
            ->leftJoin('departamentos as d', 'pr.departamento_id', '=', 'd.id')
            ->where('pla.escenario_id', $escenario->id)
            ->where('pla.tipo', 'AM_DT')
            ->selectRaw("
                pla.nivel_riesgo AS nivel,
                SUM(pla.poblacion) AS total_poblacion,
                SUM(pla.vivienda) AS total_vivienda,
                COUNT(DISTINCT dr.id) AS total_distritos,
                ARRAY_AGG(DISTINCT COALESCE(d.nombre)) AS departamentos
            ")
            ->whereNotNull('pla.nivel_riesgo')
            ->groupBy('pla.nivel_riesgo')
            ->orderByRaw("
                CASE UPPER(pla.nivel_riesgo)
                    WHEN 'MA' THEN 1
                    WHEN 'A' THEN 2
                    WHEN 'M' THEN 3
                    WHEN 'B' THEN 4
                    WHEN 'MB' THEN 5
                    ELSE 6
                END
            ")
            ->get();

        return [
            'inundaciones' => $inundaciones,
        ];
    }
}
