<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CopyImporterPlantilla
{
    public static function importCsvToPlantillaA(string $csvAbsolutePath, int $escenarioId): void
    {
        $csvAbs = Storage::disk('local')->path($csvAbsolutePath);
        $utf8Path = ConvertUTF8::ensureUtf8($csvAbs);

        DB::beginTransaction();
        try {
            DB::statement("SET synchronous_commit = OFF");
            DB::statement("TRUNCATE TABLE plantilla_a_staging");

            // 1) Carga cruda a staging (HEADER si tu CSV tiene encabezado)
            DB::statement("
                COPY plantilla_a_staging
                FROM " . DB::getPdo()->quote($utf8Path) . "
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

                COALESCE(NULLIF(replace(regexp_replace(vias, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.000),

                COALESCE(NULLIF(replace(regexp_replace(upper(superficie_agricola), '[^0-9E+,\.-]', '', 'g'),',', '.'),'')::numeric, 0.000),

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

                COALESCE(NULLIF(replace(regexp_replace(sa_riego, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.000),

                COALESCE(NULLIF(replace(regexp_replace(sa_secano, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.000),

                COALESCE(ROUND(NULLIF(replace(prod_agropecuarios, ',', '.'), '')::numeric)::int, 0),

                COALESCE(NULLIF(replace(regexp_replace(prod_agropecuarios_65, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.000),

                COALESCE(NULLIF(replace(regexp_replace(superficie_de_pastos, '[^0-9,.\-]', '', 'g'), ',', '.'),'')::numeric, 0.000),

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

    public static function importCsvToPlantillaB(string $csvAbsolutePath, int $escenarioId): void
    {
        $csvAbs = Storage::disk('local')->path($csvAbsolutePath);
        $utf8Path = ConvertUTF8::ensureUtf8($csvAbs);

        DB::beginTransaction();
        try {
            DB::statement("SET synchronous_commit = OFF");
            DB::statement("TRUNCATE TABLE plantilla_b_staging");

            // 1) Carga cruda a staging (HEADER si tu CSV tiene encabezado)
            DB::statement("
                COPY plantilla_b_staging
                FROM " . DB::getPdo()->quote($utf8Path) . "
                WITH (FORMAT csv, HEADER true, DELIMITER ';', QUOTE '\"',
                ESCAPE '\"',
                NULL '',
                ENCODING 'UTF8')
            ");

            // 2) Inserta en la final con casts/LPAD/valores por defecto
            DB::statement("
            INSERT INTO plantilla_b (
                escenario_id,ubigeo,viviendas,poblacion,red_agua,reservorios,ptar,ptap,grupos_vulnerables,material_pared_predominante,red_vial_nacional,
                red_vial_departamental,red_vial_vecinal,puentes,red_ferroviaria,aerodromos,puertos,
                locales_educativos,poblacion_indigena,bienes_inmuebles,patrimonio_historico,museos,
                es_cr,es,vulnerabilidad,nivel_peligro_sismo,nivel_peligro_tsunami,nivel_peligro_glaciar,
                nivel_peligro_movimientos_masa,valor_riesgo_sismo,valor_riesgo_tsunami,valor_riesgo_glaciar,
                valor_riesgo_movimientos_masa,nr_sismo,nr_tsunami,nr_glaciar,nr_movimientos_masa,created_at, updated_at
            )
            SELECT
                -- 1) FK
                :escenario_id,

                -- 3) ubigeo a 10 dígitos (izq con ceros)
                lpad(NULLIF(ubigeo,'')::text, 6, '0'),

                -- 4) enteros (COALESCE -> 0)
                COALESCE(ROUND(NULLIF(replace(regexp_replace(viviendas,               '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(poblacion,               '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(red_agua,                '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(reservorios,             '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(ptar,                    '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(ptap,                    '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(grupos_vulnerables,      '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(material_pared_predominante,'[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),

                -- 5) decimales (numeric(8,3) en schema)
                COALESCE(NULLIF(replace(regexp_replace(red_vial_nacional,     '[^0-9E+,.\-]', '', 'g'), ',', '.'), '')::numeric, 0.000),
                COALESCE(NULLIF(replace(regexp_replace(red_vial_departamental,'[^0-9E+,.\-]', '', 'g'), ',', '.'), '')::numeric, 0.000),
                COALESCE(NULLIF(replace(regexp_replace(red_vial_vecinal,      '[^0-9E+,.\-]', '', 'g'), ',', '.'), '')::numeric, 0.000),

                -- 6) más enteros
                COALESCE(ROUND(NULLIF(replace(regexp_replace(puentes,         '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),

                -- 7) decimal
                COALESCE(NULLIF(replace(regexp_replace(red_ferroviaria,       '[^0-9E+,.\-]', '', 'g'), ',', '.'), '')::numeric, 0.000),

                -- 8) enteros
                COALESCE(ROUND(NULLIF(replace(regexp_replace(aerodromos,             '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(puertos,                '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(locales_educativos,     '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(poblacion_indigena,     '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(bienes_inmuebles,       '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(patrimonio_historico,   '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(museos,                 '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(es_cr,                  '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),
                COALESCE(ROUND(NULLIF(replace(regexp_replace(es,                     '[^0-9,.\-]', '', 'g'), ',', '.'), '')::numeric)::int, 0),

                -- 9) textos (nullable)
                NULLIF(TRIM(vulnerabilidad), ''),
                NULLIF(TRIM(nivel_peligro_sismo), ''),
                NULLIF(TRIM(nivel_peligro_tsunami), ''),
                NULLIF(TRIM(nivel_peligro_glaciar), ''),
                NULLIF(TRIM(nivel_peligro_movimientos_masa), ''),

                -- 10) decimales riesgos (numeric(8,3))
                COALESCE(NULLIF(replace(regexp_replace(valor_riesgo_sismo,            '[^0-9E+,.\-]', '', 'g'), ',', '.'), '')::numeric, 0.000),
                COALESCE(NULLIF(replace(regexp_replace(valor_riesgo_tsunami,          '[^0-9E+,.\-]', '', 'g'), ',', '.'), '')::numeric, 0.000),
                COALESCE(NULLIF(replace(regexp_replace(valor_riesgo_glaciar,          '[^0-9E+,.\-]', '', 'g'), ',', '.'), '')::numeric, 0.000),
                COALESCE(NULLIF(replace(regexp_replace(valor_riesgo_movimientos_masa, '[^0-9E+,.\-]', '', 'g'), ',', '.'), '')::numeric, 0.000),

                -- 11) códigos NR (nullable strings)
                NULLIF(TRIM(nr_sismo), ''),
                NULLIF(TRIM(nr_tsunami), ''),
                NULLIF(TRIM(nr_glaciar), ''),
                NULLIF(TRIM(nr_movimientos_masa), ''),

                -- 12) timestamps
                NOW(),
                NOW()
            FROM plantilla_b_staging
        ", ['escenario_id' => $escenarioId]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
