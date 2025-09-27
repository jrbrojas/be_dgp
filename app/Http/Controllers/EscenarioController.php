<?php

namespace App\Http\Controllers;

use App\Exports\PlantillaExport;
use App\Http\Requests\EscenarioStoreRequest;
use App\Imports\EscenarioImport;
use App\Models\Escenario;
use App\Models\Formulario;
use App\Models\PlantillaA;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EscenarioController extends Controller
{
    public function index(Request $request)
    {
        $escenarios = Escenario::with('formulario')
            ->search($request['query'])
            ->orderBy('created_at', 'desc')
            ->get();
        // eviar los parametros de esta forma para que el datatable del front los pueda leer sin problemas
        return response()->json([
            'list' => $escenarios,
            'total' => $escenarios->count(),
        ]);
    }

    public function formulariosFull()
    {
        $formularios = Formulario::get();
        // eviar los parametros de esta forma para que el datatable del front los pueda leer sin problemas
        return response()->json([
            'list' => $formularios,
        ]);
    }

    public function show(Escenario $escenario)
    {

        // $data = PlantillaA::getByFormularioAvisoMeteorologico($escenario);
        $data = Escenario::getByFormulario($escenario);

        return response()->json([
            'escenario' => $escenario->load('formulario'),
            'plantillas' => $data,
        ]);
    }

    public function store(EscenarioStoreRequest $request)
    {
        $data = $request->validated();
        $escenario = DB::transaction(function () use ($request, $data) {
            $data['plantilla_subida'] = $this->storeFile($request->file('plantilla'));
            $data['excel'] = $this->storeFile($request->file('excel'));
            $data['mapa_centro'] = $this->storeFile($request->file('mapa_centro'), 'public');
            $data['mapa_izquierdo'] = $this->storeFile($request->file('mapa_izquierdo'), 'public');
            $data['mapa_derecho'] = $this->storeFile($request->file('mapa_derecho'), 'public');
            return Escenario::create($data);
        });

        // procesar la plantilla
        if ($request->file('plantilla')) {
            Excel::queueImport(new EscenarioImport($escenario->id), $request->file('plantilla'));
        }

        return response()->json(['message' => 'Escenario creado correctamente!']);
    }

    public function update(EscenarioStoreRequest $request, Escenario $escenario)
    {
        $data = $request->validated();

        if ($request->file('plantilla')) {
            $urlPlantillaSubida = $this->storeFile($request->file('plantilla'));
            if (!empty($escenario->plantilla_subida)) {
                $this->deleteFile($escenario->plantilla_subida);
            }
            $escenario->plantillasA()->delete();
            $data['plantilla_subida'] = $urlPlantillaSubida;
            Excel::queueImport(new EscenarioImport($escenario->id), $request->file('plantilla'));
        }

        if ($request->file('excel')) {
            $urlExcel = $this->storeFile($request->file('excel'));
            if (!empty($escenario->excel)) {
                $this->deleteFile($escenario->excel);
            }
            $data['excel'] = $urlExcel;
        }

        if ($request->file('mapa_centro')) {
            $urlMapaCentro = $this->storeFile($request->file('mapa_centro'), 'public');
            if (!empty($escenario->mapa_centro)) {
                $this->deleteFile($escenario->mapa_centro, 'public');
            }
            $data['mapa_centro'] = $urlMapaCentro;
        }

        if ($request->file('mapa_izquierdo')) {
            $urlMapaIzquierdo = $this->storeFile($request->file('mapa_izquierdo'), 'public');
            if (!empty($escenario->mapa_izquierdo)) {
                $this->deleteFile($escenario->mapa_izquierdo, 'public');
            }
            $data['mapa_izquierdo'] = $urlMapaIzquierdo;
        }

        if ($request->file('mapa_derecho')) {
            $urlMapaDerecho = $this->storeFile($request->file('mapa_derecho'), 'public');
            if (!empty($escenario->mapa_derecho)) {
                $this->deleteFile($escenario->mapa_derecho, 'public');
            }
            $data['mapa_derecho'] = $urlMapaDerecho;
        }

        DB::transaction(function () use ($request, $escenario, $data) {
            $escenario->update($data);
        });

        return response()->json(['message' => 'Escenario actualizado correctamente!']);
    }

    public function destroy(Request $request, Escenario $escenario)
    {
        return DB::transaction(function () use ($escenario) {
            if ($escenario->plantilla_subida) {
                $this->deleteFile($escenario->plantilla_subida);
            }

            if ($escenario->excel) {
                $this->deleteFile($escenario->excel);
            }

            if ($escenario->mapa_centro) {
                $this->deleteFile($escenario->mapa_centro);
            }

            if ($escenario->mapa_izquierdo) {
                $this->deleteFile($escenario->mapa_izquierdo);
            }

            if ($escenario->mapa_derecho) {
                $this->deleteFile($escenario->mapa_derecho);
            }

            $escenario->delete();
            return response()->json(['message' => 'Escenario eliminado exitosamente!']);
        });
    }

    public function storeFile(?UploadedFile $file, string $acceso = 'local'): string
    {
        if (null === $file) {
            return "";
        }
        $timestamp = now()->timestamp;
        $filename = $timestamp . '-' . $file->getClientOriginalName();
        return $file->storeAs('escenarios', $filename, "$acceso");
    }

    public function deleteFile(string $path, string $acceso = 'local')
    {
        if ($path && Storage::disk("$acceso")->exists($path)) {
            Storage::disk("$acceso")->delete($path);
        }
    }

    public function downloadPlantilla(Request $request, Escenario $escenario)
    {
        $data = $request->input('data', []);
        return Excel::download(new PlantillaExport($data), "plantilla_$escenario->id.xlsx");
    }
}
