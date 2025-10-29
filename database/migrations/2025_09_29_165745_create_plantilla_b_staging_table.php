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

    public function up(): void
    {
        DB::statement('DROP TABLE IF EXISTS plantilla_b_staging');
        DB::statement(
            <<<SQL
            CREATE UNLOGGED TABLE plantilla_b_staging (
                ubigeo text,
                viviendas text,
                poblacion text,
                red_agua text,
                reservorios text,
                ptar text,
                ptap text,
                grupos_vulnerables text,
                material_pared_predominante text,
                red_vial_nacional text,
                red_vial_departamental text,
                red_vial_vecinal text,
                puentes text,
                red_ferroviaria text,
                aerodromos text,
                puertos text,
                locales_educativos text,
                poblacion_indigena text,
                bienes_inmuebles text,
                patrimonio_historico text,
                museos text,
                es_cr text,
                es text,
                vulnerabilidad text,
                nivel_peligro_sismo text,
                nivel_peligro_tsunami text,
                nivel_peligro_glaciar text,
                nivel_peligro_movimientos_masa text,
                valor_riesgo_sismo text,
                valor_riesgo_tsunami text,
                valor_riesgo_glaciar text,
                valor_riesgo_movimientos_masa text,
                nr_sismo text,
                nr_tsunami text,
                nr_glaciar text,
                nr_movimientos_masa text
            )
            SQL
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS plantilla_b_staging');
    }
};
