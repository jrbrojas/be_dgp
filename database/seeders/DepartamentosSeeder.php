<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DepartamentosSeeder extends Seeder
{

    public function run(): void
    {
        $path = 'departamentos.csv';
        $file = Storage::disk('local')->path($path);

        if (!file_exists($file)) {
            $this->command->error("No se encontró el archivo: $file");
            return;
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Leer cabecera

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            Departamento::updateOrCreate(
                ['id' => $data['id']],
                [
                    'codigo' => $data['codigo'],
                    'nombre' => $data['nombre'],
                    'capital' => $data['capital'],
                    'created_at' => $data['created_at'] ?: now(),
                    'updated_at' => $data['updated_at'] ?: now(),
                ]
            );
        }

        fclose($handle);
    }
}
