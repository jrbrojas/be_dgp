<?php

namespace Database\Seeders;

use App\Models\Provincia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProvinciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = 'provincias.csv';
        $file = Storage::disk('local')->path($path);

        if (!file_exists($file)) {
            $this->command->error("No se encontró el archivo: $file");
            return;
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Leer cabecera

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            Provincia::updateOrCreate(
                ['id' => $data['id']],
                [
                    'departamento_id' => $data['departamento_id'],
                    'codigo' => $data['codigo'],
                    'nombre' => $data['nombre'],
                    'latitud' => $data['latitud'],
                    'longitud' => $data['longitud'],
                ]
            );
        }

        fclose($handle);
    }
}
