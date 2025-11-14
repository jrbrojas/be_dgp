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
            "CREATE VIEW incendios_forestales_nacionales AS
            SELECT
                pla.*,
                CASE WHEN pla.tipo = 'IF_NAC_CP' THEN d1.nombre ELSE d2.nombre END AS departamento,
                CASE WHEN pla.tipo = 'IF_NAC_CP' THEN pr1.nombre ELSE pr2.nombre END AS provincia,
                CASE WHEN pla.tipo = 'IF_NAC_CP' THEN dr1.nombre ELSE dr2.nombre END AS distrito
            FROM public.plantilla_a pla
                -- LEFT JOIN centro_poblados cp ON pla.tipo = 'IF_NAC_CP' AND pla.cod_cp = cp.codigo
                LEFT JOIN distritos dr1 ON LEFT(pla.cod_cp, 6) = dr1.codigo
                LEFT JOIN provincias pr1 ON dr1.provincia_id = pr1.id
                LEFT JOIN departamentos d1 ON pr1.departamento_id = d1.id
                LEFT JOIN distritos dr2 ON pla.tipo <> 'IF_NAC_CP' AND pla.cod_ubigeo = dr2.codigo
                LEFT JOIN provincias pr2 ON dr2.provincia_id = pr2.id
                LEFT JOIN departamentos d2 ON pr2.departamento_id = d2.id
            WHERE pla.escenario_id = (
                SELECT MAX(id)
                FROM public.escenarios
                WHERE formulario_id = 7
            );"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS incendios_forestales_nacionales");
    }
};
