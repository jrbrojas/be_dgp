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
            ['nombre' => 'LLUVIAS_AVISO_METEOROLOGICO', 'peligro' => 'INUNDACION-MM', 'plantilla' => 'A'],
            ['nombre' => 'LLUVIAS_AVISO_TRIMESTRAL', 'peligro' => 'INUNDACION-MM', 'plantilla' => 'A'],
            ['nombre' => 'LLUVIAS_INFORMACION_CLIMATICA', 'peligro' => 'INUNDACION-MM', 'plantilla' => 'A'],
            ['nombre' => 'BAJAS_TEMP_AVISO_METEOROLOGICO', 'peligro' => 'FRIAJES-DESCENSO-TEMPE', 'plantilla' => 'A' ],
            ['nombre' => 'BAJAS_TEMP_AVISO_TRIMESTRAL', 'peligro' => 'DESCENSO-TEMPE', 'plantilla' => 'A'],
            ['nombre' => 'BAJAS_TEMP_INFORMACION_CLIMATICA', 'peligro' => 'HELADAS-FRIAJES', 'plantilla' => 'A'],
            ['nombre' => 'INCENDIOS_FORESTALES_NACIONAL', 'peligro' => 'INCENDIOS FORESTALES', 'plantilla' => 'A'],
            ['nombre' => 'INCENDIOS_FORESTALES_REGIONAL', 'peligro' => 'INCENDIOS FORESTALES', 'plantilla' => 'A'],
            ['nombre' => 'SISMOS_TSUNAMI_NACIONAL', 'peligro' => 'SISMO-TSUNAMI-GLACIARES-MM', 'plantilla' => 'B'],
            ['nombre' => 'SISMOS_TSUNAMI_OTROS_AMBITOS', 'peligro' => 'SISMO-TSUNAMI', 'plantilla' => 'A'],
            ['nombre' => 'SEQUIAS_NACIONAL', 'peligro' => 'SEQUIAS', 'plantilla' => 'A'],
            ['nombre' => 'SEQUIAS_DEPARTAMENTAL', 'peligro' => 'SEQUIAS', 'plantilla' => 'A'],
            ['nombre' => 'VOLCANES_NACIONAL', 'peligro' => 'VOLCANES', 'plantilla' => 'A'],
        ]);
    }
}
