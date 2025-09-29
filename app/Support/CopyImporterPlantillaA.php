<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class CopyImporterPlantillaA
{
    public static function importCsvToPlantillaA(string $csvAbsolutePath, int $escenarioId): void
    {
        self::copyCsvIntoStaging($csvAbsolutePath);

        // 2) INSERT ... SELECT -> tabla final con transformaciones
        DB::statement("SET synchronous_commit = OFF");
        DB::statement(<<<SQL
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
            SQL, ['escenario_id' => $escenarioId]);
    }

    protected static function copyCsvIntoStaging(string $csvAbsolutePath): void
    {
        // Limpia staging y baja el fsync para la sesión de carga
        DB::statement("TRUNCATE TABLE plantilla_a_staging");
        DB::statement("SET synchronous_commit = OFF");

        // Abre conexión pgsql nativa (necesaria para COPY FROM STDIN)
        $host = env('DB_HOST');
        $port = env('DB_PORT', 5432);
        $db   = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');

        $connStr = sprintf("host=%s port=%d dbname=%s user=%s password=%s",
            $host, $port, $db, $user, $pass);

        $conn = @pg_connect($connStr);
        if (!$conn) {
            throw new \RuntimeException('No se pudo conectar con pgsql (extensión pgsql requerida).');
        }

        // Inicia transacción y COPY
        if (!pg_query($conn, "BEGIN")) {
            throw new \RuntimeException('No se pudo iniciar transacción pgsql.');
        }

        $copySql = "COPY plantilla_a_staging FROM STDIN WITH (FORMAT csv, HEADER true, DELIMITER ',', QUOTE '\"', ENCODING 'UTF8')";
        if (!pg_query($conn, $copySql)) {
            pg_query($conn, "ROLLBACK");
            throw new \RuntimeException('No se pudo iniciar COPY FROM STDIN.');
        }

        // Stream del archivo línea a línea hacia COPY
        $h = fopen($csvAbsolutePath, 'rb');
        if (!$h) {
            pg_query($conn, "ROLLBACK");
            throw new \RuntimeException("No se pudo abrir el archivo: {$csvAbsolutePath}");
        }

        while (!feof($h)) {
            $line = fgets($h);
            if ($line === false) { break; }
            // Envía la línea cruda; COPY lo parsea
            if (!pg_put_line($conn, $line)) {
                fclose($h);
                pg_query($conn, "ROLLBACK");
                throw new \RuntimeException('Error al escribir datos en COPY.');
            }
        }
        fclose($h);

        // Finaliza COPY y commit
        if (!pg_end_copy($conn)) {
            pg_query($conn, "ROLLBACK");
            throw new \RuntimeException('pg_end_copy falló.');
        }

        if (!pg_query($conn, "COMMIT")) {
            throw new \RuntimeException('No se pudo hacer COMMIT en pgsql.');
        }

        pg_close($conn);
    }
}
