<?php

namespace App\Imports;

use App\Models\PlantillaA;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class EscenarioImport implements ToCollection, WithStartRow, WithChunkReading
{
    protected $escenario_id;
    protected $formulario_id;

    public function __construct($escenario_id, $formulario_id)
    {
        $this->escenario_id = $escenario_id;
        $this->formulario_id = $formulario_id;
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
                'formulario_id' => $this->formulario_id,
                'tipo' => $row[0],
                'cod_cp' => $row[1],
                'cod_ubigeo' => $row[2],
                'poblacion' => $row[3],
                'vivienda' => $row[4],
                'ie' => $row[5],
                'is' => $row[6],
                'nivel_riesgo' => $row[7],
                'nivel_riesgo_agricola' => $row[8],
                'nivel_riesgo_pecuario' => $row[9],
                'cantidad_cp' => $row[10],
                'nivel_susceptibilidad' => $row[11],
                'nivel_exposicion_1_mm' => $row[12],
                'nivel_exposicion_2_inu' => $row[13],
                'nivel_exposicion_3_bt' => $row[14],
                'alumnos' => $row[15],
                'docentes' => $row[16],
                'vias' => $row[17],
                'superficie_agricola' => $row[18],
                'pob_5' => $row[19],
                'pob_60' => $row[20],
                'pob_urb' => $row[21],
                'pob_rural' => $row[22],
                'viv_tipo1' => $row[23],
                'viv_tipo2' => $row[24],
                'viv_tipo3' => $row[25],
                'viv_tipo4' => $row[26],
                'viv_tipo5' => $row[27],
                'hogares' => $row[28],
                'sa_riego' => $row[29],
                'sa_secano' => $row[30],
                'prod_agropecuarios' => $row[31],
                'prod_agropecuarios_65' => $row[32],
                'superficie_de_pastos' => $row[33],
                'alpacas' => $row[34],
                'ovinos' => $row[35],
                'vacunos' => $row[36],
                'areas_naturales' => $row[37],
                'nivel_sequia' => $row[38]
            ];
        }
        PlantillaA::insert($data);
    }
}
