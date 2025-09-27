<?php

namespace App\Imports;

use App\Events\ExcelImportCompleted;
use Illuminate\Support\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\AfterImport;

class EscenarioImport implements ToCollection, WithStartRow, WithChunkReading, ShouldQueue
{
    use Queueable;
    protected $escenario_id;

    public function __construct($escenario_id)
    {
        $this->escenario_id = $escenario_id;
    }

    public function startRow(): int
    {
        return 2; // Comenzar desde la fila 2 (A2) para no tomar los encabezados
    }

    public function chunkSize(): int
    {
        return 1000; // procesa de 1000 en 1000 filas
    }

    public function collection(Collection $rows)
    {
        $data = [];
        foreach ($rows as $row) {
            $data[] = [
                'escenario_id' => $this->escenario_id,
                'tipo' => $row[0],
                'cod_cp' => $row[1] ? str_pad($row[1], 10, '0', STR_PAD_LEFT) : '',
                'cod_ubigeo' => $row[2] ? str_pad($row[2], 6, '0', STR_PAD_LEFT) : '',
                'poblacion' => $row[3] ?? 0,
                'vivienda' => $row[4] ?? 0,
                'ie' => $row[5] ?? 0,
                'es' => $row[6] ?? 0,
                'nivel_riesgo' => $row[7],
                'nivel_riesgo_agricola' => $row[8],
                'nivel_riesgo_pecuario' => $row[9],
                'cantidad_cp' => $row[10] ?? 0,
                'nivel_susceptibilidad' => $row[11],
                'nivel_exposicion_1_mm' => $row[12],
                'nivel_exposicion_2_inu' => $row[13],
                'nivel_exposicion_3_bt' => $row[14],
                'alumnos' => $row[15] ?? 0,
                'docentes' => $row[16] ?? 0,
                'vias' => $row[17] ?? 0.00,
                'superficie_agricola' => $row[18] ?? 0.00,
                'pob_5' => $row[19] ?? 0,
                'pob_60' => $row[20] ?? 0,
                'pob_urb' => $row[21] ?? 0,
                'pob_rural' => $row[22] ?? 0,
                'viv_tipo1' => $row[23] ?? 0,
                'viv_tipo2' => $row[24] ?? 0,
                'viv_tipo3' => $row[25] ?? 0,
                'viv_tipo4' => $row[26] ?? 0,
                'viv_tipo5' => $row[27] ?? 0,
                'hogares' => $row[28] ?? 0,
                'sa_riego' => $row[29] ?? 0.00,
                'sa_secano' => $row[30] ?? 0.00,
                'prod_agropecuarios' => $row[31] ?? 0,
                'prod_agropecuarios_65' => $row[32] ?? 0.00,
                'superficie_de_pastos' => $row[33] ?? 0.00,
                'alpacas' => $row[34] ?? 0,
                'ovinos' => $row[35] ?? 0,
                'vacunos' => $row[36] ?? 0,
                'areas_naturales' => $row[37] ?? 0,
                'nivel_sequia' => $row[38]
            ];
        }
        DB::table('plantilla_a')->insert($data);
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function () {
                event(new ExcelImportCompleted($this->escenario_id));
            },
        ];
    }
}
