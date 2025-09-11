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
            ['nombre' => 'LLUVIAS_AVISO_METEOROLOGICO', 'peligro' => 'LLUVIAS', 'plantilla' => 'A'],
            ['nombre' => 'LLUVIAS_AVISO_TRIMESTRAL', 'peligro' => 'LLUVIAS', 'plantilla' => 'A'],
            ['nombre' => 'LLUVIAS_INFORMACION_CLIMATICA', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
            ['nombre' => 'BAJAS_TEMP_AVISO_METEOROLOGICO', 'peligro' => 'CLIMATICO', 'plantilla' => 'A' ],
            ['nombre' => 'BAJAS_TEMP_AVISO_TRIMESTRAL', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
            ['nombre' => 'BAJAS_TEMP_INFORMACION_CLIMATICA', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
            ['nombre' => 'INCENDIOS_FORESTALES_NACIONAL', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
            ['nombre' => 'INCENDIOS_FORESTALES_REGIONAL', 'peligro' => 'CLIMATICO', 'plantilla' => 'B'],
            ['nombre' => 'SISMOS_TSUNAMI_NACIONAL', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
            ['nombre' => 'SISMOS_TSUNAMI_OTROS_AMBITOS', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
            ['nombre' => 'SEQUIAS_NACIONAL', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
            ['nombre' => 'SEQUIAS_DEPARTAMENTAL', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
            ['nombre' => 'VOLCANES_NACIONAL', 'peligro' => 'CLIMATICO', 'plantilla' => 'A'],
        ]);
    }
}
