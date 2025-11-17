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
            "CREATE VIEW escenarios_unificados AS
                SELECT
                1::int AS formulario_id,
                'Lluvias'::text AS fenomeno,
                'Meteorológico'::text AS pronostico,
                CASE
                    WHEN f1.tipo IN ('INU_CP', 'INU_IE', 'INU_ES') THEN 'Inundaciones'
                    ELSE 'Mov. Masa'
                END AS peligro,
                CASE
                    WHEN f1.tipo IN ('INU_CP', 'INU_IE', 'INU_ES') THEN f1.nivel_exposicion_2_inu
                    ELSE f1.nivel_riesgo
                END AS nivel,
                f1.*
                FROM lluvias_aviso_meteorologico f1

                UNION ALL

                SELECT
                    2,
                    'Lluvias' AS fenomeno,
                    'Trimestral' as pronostico,
                    CASE
                        WHEN f2.nivel_exposicion_2_inu IS NOT NULL THEN 'Inundaciones'
                        ELSE 'Mov. Masa'
                    END AS peligro,
                    CASE
                        WHEN f2.nivel_exposicion_2_inu IS NOT NULL THEN f2.nivel_exposicion_2_inu
                        ELSE f2.nivel_exposicion_1_mm
                    END AS nivel,
                    f2.*
                FROM lluvias_aviso_trimestral f2

                UNION ALL

                SELECT
                    3,
                    'Lluvias' AS fenomeno,
                    'Climatica' as pronostico,
                    CASE
                        WHEN f3.tipo IN ('CLI_INU') THEN 'Inundaciones' ELSE 'Mov. Masa'
                    END AS peligro,
                    f3.nivel_riesgo AS nivel,
                    f3.*
                FROM lluvias_informacion_climatica f3

                UNION ALL

                SELECT
                    4,
                    'Bajas Temperaturas' AS fenomeno,
                    'Meteorológico' as pronostico,
                    'Bajas Temperaturas' AS peligro,
                    CASE
                        WHEN f4.nivel_riesgo = 'MA' THEN 'Muy Alto'
                        WHEN f4.nivel_riesgo = 'A'  THEN 'Alto'
                        WHEN f4.nivel_riesgo = 'M'  THEN 'Medio'
                        WHEN f4.nivel_riesgo = 'B'  THEN 'Bajo'
                        WHEN f4.nivel_riesgo = 'MB' THEN 'Muy Bajo'
                        ELSE f4.nivel_riesgo  -- por si aparece otro valor inesperado
                    END AS nivel,
                    f4.*
                FROM bajas_temp_aviso_meteorologico f4

                UNION ALL

                SELECT
                    5,
                    'Bajas Temperaturas' AS fenomeno,
                    'Trimestral' as pronostico,
                    'Bajas Temperaturas' AS peligro,
                    CASE
                        WHEN f5.nivel_exposicion_3_bt = 'MA' THEN 'Muy Alto'
                        WHEN f5.nivel_exposicion_3_bt = 'A'  THEN 'Alto'
                        WHEN f5.nivel_exposicion_3_bt = 'M'  THEN 'Medio'
                        WHEN f5.nivel_exposicion_3_bt = 'B'  THEN 'Bajo'
                        WHEN f5.nivel_exposicion_3_bt = 'MB' THEN 'Muy Bajo'
                        ELSE f5.nivel_exposicion_3_bt  -- por si aparece otro valor inesperado
                    END AS nivel,
                    f5.*
                FROM bajas_temp_aviso_trimestral f5

                UNION ALL

                SELECT
                    6,
                    'Bajas Temperaturas' AS fenomeno,
                    'Climatica' as pronostico,
                    'Bajas Temperaturas' AS peligro,
                    f6.nivel_riesgo AS nivel,
                    f6.*
                FROM bajas_temp_informacion_climatica f6

                UNION ALL

                SELECT
                    7,
                    'Incendios Forestales' AS fenomeno,
                    'F. Nacional' as pronostico,
                    'Incendios Forestales' AS peligro,
                    f7.nivel_riesgo AS nivel,
                    f7.*
                FROM incendios_forestales_nacionales f7

                UNION ALL

                SELECT
                    8,
                    'Incendios Forestales' AS fenomeno,
                    'F. Regional' as pronostico,
                    'Incendios Forestales' AS peligro,
                    f8.nivel_riesgo AS nivel,
                    f8.*
                FROM incendios_forestales_regionales f8"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS escenarios_unificados");
    }
};
