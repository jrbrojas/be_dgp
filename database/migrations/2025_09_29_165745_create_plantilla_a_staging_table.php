<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    protected $connection = 'pgsql';

    public function up(): void
    {
        DB::statement('DROP TABLE IF EXISTS plantilla_a_staging');
        DB::statement(
            <<<SQL
            CREATE UNLOGGED TABLE plantilla_a_staging (
                tipo text,
                cod_cp text,
                cod_ubigeo text,
                poblacion text,
                vivienda text,
                ie text,
                es text,
                nivel_riesgo text,
                nivel_riesgo_agricola text,
                nivel_riesgo_pecuario text,
                cantidad_cp text,
                nivel_susceptibilidad text,
                nivel_exposicion_1_mm text,
                nivel_exposicion_2_inu text,
                nivel_exposicion_3_bt text,
                alumnos text,
                docentes text,
                vias text,
                superficie_agricola text,
                pob_5 text,
                pob_60 text,
                pob_urb text,
                pob_rural text,
                viv_tipo1 text,
                viv_tipo2 text,
                viv_tipo3 text,
                viv_tipo4 text,
                viv_tipo5 text,
                hogares text,
                sa_riego text,
                sa_secano text,
                prod_agropecuarios text,
                prod_agropecuarios_65 text,
                superficie_de_pastos text,
                alpacas text,
                ovinos text,
                vacunos text,
                areas_naturales text,
                nivel_sequia text
            )
            SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS plantilla_a_staging');
    }
};
