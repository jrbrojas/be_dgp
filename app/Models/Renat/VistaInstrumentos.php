<?php

namespace App\Models\Renat;

use App\View\Components\FormatNombreArray;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VistaInstrumentos extends Model
{
    protected $connection = 'pgsql_renat';
    // protected $table = 'vista_unificada_planes_instrumentos_fortalecimiento';
    protected $table = 'view_instrumentos';
    public $incrementing = false;
    public $timestamps = false;

    public static function instrumentosPorNivel(array $data)
    {
        $instrumentos = [];

        foreach ($data as $tipo => $items) {

            // 1) Colecciona TODOS los departamentos de ese tipo (unión)
            $departamentosTipo = collect($items)
                ->flatMap(function ($item) {
                    // $it->departamentos es el string PG array
                    $parser = new FormatNombreArray($item->departamentos ?? null);
                    return $parser->getItems(); // retorna ['APURIMAC','AREQUIPA','SAN MARTIN', ...]
                })
                ->map(fn($d) => trim($d))     // por si acaso
                ->filter()                     // quita vacíos
                ->unique()
                ->values();

            if ($departamentosTipo->isEmpty()) {
                $instrumentos[$tipo] = [];
                continue;
            }

            // 2) UNA sola consulta por tipo (no por item). Solo columnas necesarias.
            //    Si tu vista puede traer duplicados por distrito, agregamos groupBy + SUM.
            $valoresTipo = VistaInstrumentos::select([
                'departamento',
                'provincia',
                'distrito',
                DB::raw('SUM(COALESCE(pprrd,0)) AS pprrd'),
                DB::raw('SUM(COALESCE(evar,0))   AS evar'),
                DB::raw('SUM(COALESCE(reas,0))::numeric AS reas'),
            ])
                // ->where('periodo', 2025)
                ->whereIn('departamento', $departamentosTipo->all())
                ->groupBy('departamento', 'provincia', 'distrito')
                ->orderBy('departamento')->orderBy('provincia')->orderBy('distrito')
                ->get();

            // 3) Lookup en memoria: DEP -> PROV -> DIST -> {pprrd,evar,pec}
            $lookup = $valoresTipo
                ->groupBy(['departamento', 'provincia', 'distrito'])
                ->map(fn($provGroup) => $provGroup->map(
                    fn($distGroup) => $distGroup->map(
                        fn($rows) => [
                            'pprrd' => (int) ($rows->sum('pprrd')),
                            'evar'  => (int) ($rows->sum('evar')),
                            'reas'   => (float) ($rows->sum('reas')),
                        ]
                    )
                ));

            // 4) Construye estructura por nivel usando SOLO el lookup (sin más queries)
            $niveles = [];
            foreach ($items as $item) {
                $nivel = $item->nivel ?? null;
                if (!$nivel) continue;

                // depas por nivel
                $parser = new FormatNombreArray($item->departamentos ?? null);
                $departamentosNivel = collect($parser->getItems())
                    ->map(fn($d) => mb_strtoupper(trim($d)))
                    ->filter()
                    ->unique()
                    ->values();

                if (!isset($niveles[$nivel])) {
                    $niveles[$nivel] = ['nivel' => $nivel, 'departamentos' => []];
                }


                foreach ($departamentosNivel as $dep) {
                    $niveles[$nivel]['departamentos'][$dep] ??= ['provincias' => []];

                    // provincias existentes en el lookup para ese dep
                    $provGroups = $lookup->get($dep, collect());
                    foreach ($provGroups as $prov => $distGroups) {
                        $niveles[$nivel]['departamentos'][$dep]['provincias'][$prov] ??= ['distritos' => []];

                        foreach ($distGroups as $dist => $vals) {
                            // $vals ya viene agregado de la etapa 3
                            $niveles[$nivel]['departamentos'][$dep]['provincias'][$prov]['distritos'][$dist] = $vals;
                        }
                    }
                }
            }

            // Si el front prefiere lista, usa array_values($niveles)
            $instrumentos[$tipo] = array_values($niveles);
        }

        return $instrumentos;
    }
}
