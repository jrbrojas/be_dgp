<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaA extends Model
{
    protected $table = 'plantilla_a';
    protected $fillable = [
        'escenario_id',
        'formulario_id',
        'tipo',
        'cod_cp',
        'cod_ubigeo',
        'poblacion',
        'vivienda',
        'ie',
        'is',
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
}
