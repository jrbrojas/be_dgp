<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormulariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('formularios')->insert([
            ['nombre' => '1. LLUVIAS_AVISO_METEOROLOGICO', 'peligro' => 'INUNDACION-MM', 'plantilla' => 'A'],
            ['nombre' => '2. LLUVIAS_AVISO_TRIMESTRAL', 'peligro' => 'INUNDACION-MM', 'plantilla' => 'A'],
            ['nombre' => '3. LLUVIAS_INFORMACION_CLIMATICA', 'peligro' => 'INUNDACION-MM', 'plantilla' => 'A'],
            ['nombre' => '4. BAJAS_TEMP_AVISO_METEOROLOGICO', 'peligro' => 'FRIAJES-DESCENSO-TEMPE', 'plantilla' => 'A' ],
            ['nombre' => '5. BAJAS_TEMP_AVISO_TRIMESTRAL', 'peligro' => 'DESCENSO-TEMPE', 'plantilla' => 'A'],
            ['nombre' => '6. BAJAS_TEMP_INFORMACION_CLIMATICA', 'peligro' => 'HELADAS-FRIAJES', 'plantilla' => 'A'],
            ['nombre' => '7. INCENDIOS_FORESTALES_NACIONAL', 'peligro' => 'INCENDIOS FORESTALES', 'plantilla' => 'A'],
            ['nombre' => '8. INCENDIOS_FORESTALES_REGIONAL', 'peligro' => 'INCENDIOS FORESTALES', 'plantilla' => 'A'],
            ['nombre' => '9. SISMOS_TSUNAMI_NACIONAL', 'peligro' => 'SISMO-TSUNAMI-GLACIARES-MM', 'plantilla' => 'B'],
            ['nombre' => '10. SISMOS_TSUNAMI_OTROS_AMBITOS', 'peligro' => 'SISMO-TSUNAMI', 'plantilla' => 'A'],
            ['nombre' => '11. SEQUIAS_NACIONAL', 'peligro' => 'SEQUIAS', 'plantilla' => 'A'],
            ['nombre' => '12. SEQUIAS_DEPARTAMENTAL', 'peligro' => 'SEQUIAS', 'plantilla' => 'A'],
            ['nombre' => '13. VOLCANES_NACIONAL', 'peligro' => 'VOLCANES', 'plantilla' => 'A'],
        ]);
    }
}
