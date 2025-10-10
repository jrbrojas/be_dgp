<?php

namespace Database\Seeders;

use App\Models\CentroPoblado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CentroPobladosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = 'seeders/centros_poblados.csv';
        $file = Storage::disk('local')->path($path);

        if (!file_exists($file)) {
            $this->command->error("No se encontró el archivo: $file");
            return;
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Leer cabecera

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            CentroPoblado::updateOrCreate(
                ['id' => $data['id']],
                [
                    'distrito_id' => $data['distrito_id'],
                    'codigo' => $data['codigo'],
                    'nombre' => $data['nombre'],
                ]
            );
        }

        fclose($handle);
    }
}
