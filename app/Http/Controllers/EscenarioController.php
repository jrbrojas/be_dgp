<?php

namespace App\Http\Controllers;

use App\Exports\FormularioExport;
use App\Http\Requests\EscenarioStoreRequest;
use App\Imports\EscenarioImport;
use App\Models\Escenario;
use App\Models\PlantillaA;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EscenarioController extends Controller
{
    public function index()
    {
        $escenarios = Escenario::with('formulario')->get();
        // eviar los parametros de esta forma para que el datatable del front los pueda leer sin problemas
        return response()->json([
            'list' => $escenarios,
            'total' => $escenarios->count(),
        ]);
    }

    public function show(Escenario $escenario)
    {
        $escenario->formulario->plantilla === 'A' ?
        $data = PlantillaA::getByEscenario($escenario) :
        $data =  []; //$escenario->load('plantillasB');
        return response()->json([
            'escenario' => $escenario,
            'plantillas' => $data
        ]);
    }

    public function store(EscenarioStoreRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($request, $data) {
            $data['plantilla_subida'] = $this->storeFile($request->file('plantilla'));
            $data['excel'] = $this->storeFile($request->file('excel'));
            $data['mapa_centro'] = $this->storeFile($request->file('mapa_centro'));
            $data['mapa_izquierda'] = $this->storeFile($request->file('mapa_izquierda'));
            $escenario = Escenario::create($data);

            // procesar la plantilla
            if ($request->file('plantilla')) {
                Excel::import(new EscenarioImport($escenario->id, $escenario->formulario_id), $request->file('plantilla'));
            }

            return $escenario;
        });
    }

    public function update(EscenarioStoreRequest $request, Escenario $escenario)
    {
        $data = $request->validated();

        if ($request->file('plantilla_subida')) {
            $urlPlantillaSubida = $this->storeFile($request->file('plantilla_subida'));
            $this->deleteFile($escenario->plantilla_subida);
            $data['plantilla_subida'] = $urlPlantillaSubida;
        }

        if ($request->file('excel')) {
            $urlExcel = $this->storeFile($request->file('excel'));
            $this->deleteFile($escenario->excel);
            $data['excel'] = $urlExcel;
        }

        if ($request->file('mapa_centro')) {
            $urlMapaCentro = $this->storeFile($request->file('mapa_centro'));
            $this->deleteFile($escenario->mapa_centro);
            $data['mapa_centro'] = $urlMapaCentro;
        }

        if ($request->file('mapa_izquierda')) {
            $urlMapaIzquierda = $this->storeFile($request->file('mapa_izquierda'));
            $this->deleteFile($escenario->mapa_izquierdo);
            $data['mapa_izquierda'] = $urlMapaIzquierda;
        }

        $escenario->update($data);
        return $escenario;
    }

    public function destroy(Request $request, Escenario $escenario)
    {
        return DB::transaction(function() use($escenario) {
            if ($escenario->plantilla_subida) {
                $this->deleteFile($escenario->plantilla_subida);
            }

            if ($escenario->excel) {
                $this->deleteFile($escenario->excel);
            }

            if ($escenario->mapa_centro) {
                $this->deleteFile($escenario->mapa_centro);
            }

            if ($escenario->mapa_izquierda) {
                $this->deleteFile($escenario->mapa_izquierda);
            }

            $escenario->delete();
            return response()->json(['message' => 'Escenario eliminado exitosamente!']);
        });
    }

    public function storeFile(?UploadedFile $file): string
    {
        if (null === $file) {
            return "";
        }
        $timestamp = now()->timestamp;
        $filename = $timestamp . '-' . $file->getClientOriginalName();
        return $file->storeAs('escenarios', $filename, 'local');
    }

    public function deleteFile(string $path)
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
