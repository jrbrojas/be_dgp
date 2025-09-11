<?php

namespace App\Http\Controllers;

use App\Http\Requests\EscenarioStoreRequest;
use App\Models\Escenario;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EscenarioController extends Controller
{
    public function index()
    {
        return Escenario::all();
    }

    public function show(Escenario $entidad)
    {
        return $entidad;
    }

    public function guardarExcel(?UploadedFile $file): string
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

    public function store(EscenarioStoreRequest $request)
    {
        // Urls
        $urlPlantillaSubida = $this->guardarExcel($request->file('plantilla_subida'));
        $urlExcel = $this->guardarExcel($request->file('excel'));
        $urlMapaCentro = $this->guardarExcel($request->file('mapa_centro'));
        $urlMapaIzquierda = $this->guardarExcel($request->file('mapa_izquierda'));

        $entidad = new Escenario();
        $entidad->id_formulario = $request->get('id_formulario');
        $entidad->fecha_inicio = $request->get('fecha_inicio');
        $entidad->fecha_fin = $request->get('fecha_fin');
        $entidad->nombre = $request->get('nombre');
        $entidad->url_base = $request->get('url_base');
        $entidad->plantilla_subida = $urlPlantillaSubida;
        $entidad->excel = $urlExcel;
        $entidad->mapa_centro = $urlMapaCentro;
        $entidad->mapa_izquierda = $urlMapaIzquierda;

        $entidad->save();
        return $entidad;
    }

    public function eliminarArchivo(string $path)
    {
        Storage::disk('local')->delete($path);
    }

    public function update(EscenarioStoreRequest $request, Escenario $entidad)
    {
        $entidad->id_formulario = $request->get('id_formulario');
        $entidad->fecha_inicio = $request->get('fecha_inicio');
        $entidad->fecha_fin = $request->get('fecha_fin');
        $entidad->nombre = $request->get('nombre');
        $entidad->url_base = $request->get('url_base');

        if ($request->file('plantilla_subida')) {
            $urlPlantillaSubida = $this->guardarExcel($request->file('plantilla_subida'));
            $this->eliminarArchivo($entidad->plantilla_subida);
            $entidad->plantilla_subida = $urlPlantillaSubida;
        }
        if ($request->file('excel')) {
            $urlExcel = $this->guardarExcel($request->file('excel'));
            $entidad->excel = $urlExcel;
        }
        if ($request->file('mapa_centro')) {
            $urlMapaCentro = $this->guardarExcel($request->file('mapa_centro'));
            $entidad->mapa_centro = $urlMapaCentro;
        }
        if ($request->file('mapa_izquierda')) {
            $urlMapaIzquierda = $this->guardarExcel($request->file('mapa_izquierda'));
            $entidad->mapa_izquierda = $urlMapaIzquierda;
        }

        $entidad->save();
        return $entidad;
    }

    public function destroy(Escenario $entidad)
    {
        $entidad->delete();
        return $entidad;
    }
}
