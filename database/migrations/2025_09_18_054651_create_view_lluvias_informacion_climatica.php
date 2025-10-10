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
        DB::statement(
            "CREATE VIEW lluvias_informacion_climatica AS
            SELECT
                pla.*,
                d.nombre AS departamento,
                pr.nombre AS provincia,
                dr.nombre AS distrito
            FROM public.plantilla_a pla
                LEFT JOIN distritos dr ON pla.cod_ubigeo = dr.codigo
                LEFT JOIN provincias pr ON dr.provincia_id = pr.id
                LEFT JOIN departamentos d ON pr.departamento_id = d.id
            WHERE pla.escenario_id = (
                SELECT MAX(id)
                FROM public.escenarios
                WHERE formulario_id = 3
            );"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS lluvias_informacion_climatica");
    }
};
