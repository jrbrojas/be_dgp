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
        $path = 'centros_poblados.csv';
        $file = Storage::disk('local')->path($path);

        if (!file_exists($file)) {
            $this->command->error("No se encontró el archivo: $file");
            return;
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Leer cabecera

        $batchSize = 1000; // Inserta en bloques de 1000 filas
        $batchData = [];

        DB::disableQueryLog(); // evita consumo excesivo de memoria
        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);

                $batchData[] = [
                    'id'          => $data['id'],
                    'distrito_id' => $data['distrito_id'],
                    'codigo'      => str_pad($data['codigo'], 10, '0', STR_PAD_LEFT),
                    'nombre'      => $data['nombre'],
                ];

                // Cada 1000 filas insertamos
                if (count($batchData) >= $batchSize) {
                    CentroPoblado::upsert(
                        $batchData,
                        ['id'], // columnas únicas
                        ['distrito_id', 'codigo', 'nombre'] // columnas a actualizar
                    );
                    $batchData = [];
                }
            }

            // Insertar las que queden
            if (!empty($batchData)) {
                CentroPoblado::upsert(
                    $batchData,
                    ['id'],
                    ['distrito_id', 'codigo', 'nombre']
                );
            }

            DB::commit();
            fclose($handle);

            $this->command->info("✅ Centros poblados insertados/actualizados correctamente.");
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);
            $this->command->error("❌ Error: " . $e->getMessage());
        }
    }
}
