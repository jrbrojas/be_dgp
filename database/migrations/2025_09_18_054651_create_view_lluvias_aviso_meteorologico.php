<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE VIEW public.lluvias_aviso_meteorologico AS
            SELECT
                pla.*,
                CASE WHEN pla.tipo = 'INU_CP' THEN d1.nombre ELSE d2.nombre END AS departamento,
                CASE WHEN pla.tipo = 'INU_CP' THEN pr1.nombre ELSE pr2.nombre END AS provincia,
                CASE WHEN pla.tipo = 'INU_CP' THEN dr1.nombre ELSE dr2.nombre END AS distrito,
                CASE WHEN pla.tipo = 'INU_CP' THEN cp.nombre ELSE NULL END AS centro_poblado
            FROM public.plantilla_a pla
            LEFT JOIN public.centro_poblados cp ON pla.tipo = 'INU_CP' AND pla.cod_cp = cp.codigo
            LEFT JOIN public.distritos dr1 ON cp.distrito_id = dr1.id
            LEFT JOIN public.provincias pr1 ON dr1.provincia_id = pr1.id
            LEFT JOIN public.departamentos d1 ON pr1.departamento_id = d1.id
            LEFT JOIN public.distritos dr2 ON pla.tipo <> 'INU_CP' AND pla.cod_ubigeo = dr2.codigo
            LEFT JOIN public.provincias pr2 ON dr2.provincia_id = pr2.id
            LEFT JOIN public.departamentos d2 ON pr2.departamento_id = d2.id
            WHERE pla.escenario_id = (
                SELECT MAX(id) FROM public.escenarios WHERE formulario_id = 1
            );
            SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS public.lluvias_aviso_meteorologico CASCADE;');
    }
};
