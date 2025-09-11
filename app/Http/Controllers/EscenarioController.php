<?php

namespace App\Http\Controllers;

use App\Exports\FormularioExport;
use App\Http\Requests\EscenarioStoreRequest;
use App\Imports\EscenarioImport;
use App\Models\Escenario;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class EscenarioController extends Controller
{
    public function index()
    {
        return Escenario::with('formulario')->get();
    }

    public function show(Escenario $entidad)
    {
        return $entidad;
    }

    public function store(EscenarioStoreRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($request, $data) {
            $data['plantilla_subida'] = $this->storeFile($request->file('plantilla'));
            $data['excel'] = $this->storeFile($request->file('excel'));
            $data['mapa_centro'] = $this->storeFile($request->file('mapa_centro'));
            $data['mapa_izquierda'] = $this->storeFile($request->file('mapa_izquierda'));
            $entidad = Escenario::create($data);

            // procesar la plantilla
            if ($request->file('plantilla')) {
                Excel::import(new EscenarioImport($entidad->id, $entidad->formulario_id), $request->file('plantilla'));
            }

            return $entidad;
        });
    }


    public function update(EscenarioStoreRequest $request, Escenario $entidad)
    {
        $data = $request->validated();

        if ($request->file('plantilla_subida')) {
            $urlPlantillaSubida = $this->storeFile($request->file('plantilla_subida'));
            $this->deleteFile($entidad->plantilla_subida);
            $data['plantilla_subida'] = $urlPlantillaSubida;
        }

        if ($request->file('excel')) {
            $urlExcel = $this->storeFile($request->file('excel'));
            $data['excel'] = $urlExcel;
        }

        if ($request->file('mapa_centro')) {
            $urlMapaCentro = $this->storeFile($request->file('mapa_centro'));
            $data['mapa_centro'] = $urlMapaCentro;
        }

        if ($request->file('mapa_izquierda')) {
            $urlMapaIzquierda = $this->storeFile($request->file('mapa_izquierda'));
            $data['mapa_izquierda'] = $urlMapaIzquierda;
        }

        $entidad->update($data);
        return $entidad;
    }

    public function destroy(Escenario $entidad)
    {
        $entidad->delete();
        return $entidad;
    }

    public function storeFile(?UploadedFile $file): string
    {
        if (null === $file) {
            return "";
        }
        $name = $file->getClientOriginalName();
        $unixtime = time();
        $url = "escenarios/{$unixtime}-{$name}";
        Storage::disk('local')->put($url, file_get_contents($file));
        return $url;
    }

    public function deleteFile(string $path)
    {
        Storage::disk('local')->delete($path);
    }
}
