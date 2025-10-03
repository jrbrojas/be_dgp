<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CopyImporterPlantillaA
{
    public static function importCsvToPlantillaA(string $csvAbsolutePath, int $escenarioId): void
    {
        $csvAbs = Storage::disk('public')->path($csvAbsolutePath);

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
              areas_naturales, nivel_sequia
            )
            SELECT
              :escenario_id,
              tipo,
              lpad(NULLIF(cod_cp,'')::text, 10, '0'),
              lpad(NULLIF(cod_ubigeo,'')::text, 6, '0'),
              COALESCE(NULLIF(poblacion,'')::int, 0),
              COALESCE(NULLIF(vivienda,'')::int, 0),
              COALESCE(NULLIF(ie,'')::int, 0),
              COALESCE(NULLIF(es,'')::int, 0),
              nivel_riesgo,
              nivel_riesgo_agricola,
              nivel_riesgo_pecuario,
              COALESCE(NULLIF(cantidad_cp,'')::int, 0),
              nivel_susceptibilidad,
              nivel_exposicion_1_mm,
              nivel_exposicion_2_inu,
              nivel_exposicion_3_bt,
              COALESCE(NULLIF(alumnos,'')::int, 0),
              COALESCE(NULLIF(docentes,'')::int, 0),
              COALESCE(NULLIF(vias,'')::numeric, 0.00),
              COALESCE(NULLIF(superficie_agricola,'')::numeric, 0.00),
              COALESCE(NULLIF(pob_5,'')::int, 0),
              COALESCE(NULLIF(pob_60,'')::int, 0),
              COALESCE(NULLIF(pob_urb,'')::int, 0),
              COALESCE(NULLIF(pob_rural,'')::int, 0),
              COALESCE(NULLIF(viv_tipo1,'')::int, 0),
              COALESCE(NULLIF(viv_tipo2,'')::int, 0),
              COALESCE(NULLIF(viv_tipo3,'')::int, 0),
              COALESCE(NULLIF(viv_tipo4,'')::int, 0),
              COALESCE(NULLIF(viv_tipo5,'')::int, 0),
              COALESCE(NULLIF(hogares,'')::int, 0),
              COALESCE(NULLIF(sa_riego,'')::numeric, 0.00),
              COALESCE(NULLIF(sa_secano,'')::numeric, 0.00),
              COALESCE(NULLIF(prod_agropecuarios,'')::int, 0),
              COALESCE(NULLIF(prod_agropecuarios_65,'')::numeric, 0.00),
              COALESCE(NULLIF(superficie_de_pastos,'')::numeric, 0.00),
              COALESCE(NULLIF(alpacas,'')::int, 0),
              COALESCE(NULLIF(ovinos,'')::int, 0),
              COALESCE(NULLIF(vacunos,'')::int, 0),
              COALESCE(NULLIF(areas_naturales,'')::int, 0),
              nivel_sequia
            FROM plantilla_a_staging
        ", ['escenario_id' => $escenarioId]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
