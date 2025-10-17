<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CopyImporterPlantillaA
{
    public static function importCsvToPlantillaA(string $csvAbsolutePath, int $escenarioId): void
    {
        $csvAbs = Storage::disk('local')->path($csvAbsolutePath);

        DB::beginTransaction();
        try {
            DB::statement("SET synchronous_commit = OFF");
            DB::statement("TRUNCATE TABLE plantilla_a_staging");

            // 1) Carga cruda a staging (HEADER si tu CSV tiene encabezado)
            DB::statement("
                COPY plantilla_a_staging
                FROM " . DB::getPdo()->quote($csvAbs) . "
                WITH (FORMAT csv, HEADER true, DELIMITER ';', QUOTE '\"',
                ESCAPE '\"',
                NULL '',
                ENCODING 'UTF8')
            ");

            // 2) Inserta en la final con casts/LPAD/valores por defecto
            DB::statement("
            INSERT INTO plantilla_a (
              escenario_id, tipo, cod_cp, cod_ubigeo, poblacion, vivienda, ie, es,
              nivel_riesgo, nivel_riesgo_agricola, nivel_riesgo_pecuario, cantidad_cp,
              nivel_susceptibilidad, nivel_exposicion_1_mm, nivel_exposicion_2_inu, nivel_exposicion_3_bt,
              alumnos, docentes, vias, superficie_agricola, pob_5, pob_60, pob_urb, pob_rural,
              viv_tipo1, viv_tipo2, viv_tipo3, viv_tipo4, viv_tipo5, hogares, sa_riego, sa_secano,
              prod_agropecuarios, prod_agropecuarios_65, superficie_de_pastos, alpacas, ovinos, vacunos,
              areas_naturales, nivel_sequia, created_at, updated_at
            )
            SELECT
                :escenario_id,
                tipo,
                lpad(NULLIF(cod_cp,'')::text, 10, '0'),
                lpad(NULLIF(cod_ubigeo,'')::text, 6, '0'),

                COALESCE(ROUND(NULLIF(replace(poblacion, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(vivienda, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(ie, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(es, ',', '.'), '')::numeric)::int, 0),

                nivel_riesgo,
                nivel_riesgo_agricola,
                nivel_riesgo_pecuario,

                COALESCE(ROUND(NULLIF(replace(cantidad_cp, ',', '.'), '')::numeric)::int, 0),

                nivel_susceptibilidad,
                nivel_exposicion_1_mm,
                nivel_exposicion_2_inu,
                nivel_exposicion_3_bt,

                COALESCE(ROUND(NULLIF(replace(alumnos, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(docentes, ',', '.'), '')::numeric)::int, 0),

                COALESCE(NULLIF(replace(regexp_replace(vias, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.00),

                COALESCE(NULLIF(replace(regexp_replace(upper(superficie_agricola), '[^0-9E+,\.-]', '', 'g'),',', '.'),'')::numeric, 0.00),

                COALESCE(ROUND(NULLIF(replace(pob_5, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(pob_60, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(pob_urb, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(pob_rural, ',', '.'), '')::numeric)::int, 0),

                COALESCE(ROUND(NULLIF(replace(viv_tipo1, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(viv_tipo2, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(viv_tipo3, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(viv_tipo4, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(viv_tipo5, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(hogares, ',', '.'), '')::numeric)::int, 0),

                COALESCE(NULLIF(replace(regexp_replace(sa_riego, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.00),

                COALESCE(NULLIF(replace(regexp_replace(sa_secano, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.00),

                COALESCE(ROUND(NULLIF(replace(prod_agropecuarios, ',', '.'), '')::numeric)::int, 0),

                COALESCE(NULLIF(replace(regexp_replace(prod_agropecuarios_65, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.00),

                COALESCE(NULLIF(replace(regexp_replace(superficie_de_pastos, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.00),

                COALESCE(ROUND(NULLIF(replace(alpacas, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(ovinos, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(vacunos, ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(areas_naturales, ',', '.'), '')::numeric)::int, 0),

                nivel_sequia,
                NOW(),
                NOW()
            FROM plantilla_a_staging
        ", ['escenario_id' => $escenarioId]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
