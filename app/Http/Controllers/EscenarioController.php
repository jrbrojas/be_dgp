<?php

namespace App\Http\Controllers;

use App\Events\ExcelImportCompleted;
use App\Exports\PlantillaExport;
use App\Http\Requests\EscenarioStoreRequest;
use App\Imports\EscenarioImport;
use App\Models\Escenario;
use App\Models\Formulario;
use App\Models\Mapa;
use App\Support\CopyImporterPlantillaA;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Browsershot\Browsershot;

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
            'escenario' => $escenario->load(['formulario', 'mapas']),
            'plantillas' => $data,
        ]);
    }

    public function store(EscenarioStoreRequest $request)
    {
        $imagenMapas = [
            'mapa_derecho',
            'mapa_centro',
            'mapa_izquierdo',
            'mapa_izquierdo_superior',
            'mapa_izquierdo_inferior',
        ];

        $data = $request->validated();
        $escenario = DB::transaction(function () use ($request, $data, $imagenMapas) {
            $data['plantilla_subida'] = $this->storeFile($request->file('plantilla'));
            $data['excel'] = $this->storeFile($request->file('excel'));
            $escenarioData = Escenario::create($data);

            foreach ($imagenMapas as $campo) {
                if ($request->hasFile($campo)) {
                    foreach ($request->file($campo) as $file) {
                        $ruta = $this->storeFile($file, 'public');
                        Mapa::create([
                            'escenario_id' => $escenarioData->id,
                            'tipo' => $campo, // << Agrega un campo "tipo" para identificar el mapa
                            'ruta' => $ruta,  // Asegúrate de tener un campo "ruta" en la tabla
                        ]);
                    }
                }
            }

            return $escenarioData;
        });

        // procesar la plantilla
        if ($request->file('plantilla')) {

            // Excel::queueImport(new EscenarioImport($escenario->id), $request->file('plantilla'));
            $storedRelPath = $escenario->plantilla_subida; // lo que retorna storeFile (p.ej. "imports/xxxx.csv|xlsx")
            CopyImporterPlantillaA::importCsvToPlantillaA($storedRelPath, $escenario->id);
        }

        return response()->json(['message' => 'Escenario creado correctamente!']);
    }

    public function update(EscenarioStoreRequest $request, Escenario $escenario)
    {
        $imagenMapas = [
            'mapa_derecho',
            'mapa_centro',
            'mapa_izquierdo',
            'mapa_izquierdo_superior',
            'mapa_izquierdo_inferior',
        ];

        $data = $request->validated();

        if ($request->file('excel')) {
            $urlExcel = $this->storeFile($request->file('excel'));
            if (!empty($escenario->excel)) {
                $this->deleteFile($escenario->excel);
            }
            $data['excel'] = $urlExcel;
        }

        if ($request->file('plantilla')) {
            // 1) guardar nuevo archivo y eliminar el anterior
            $nuevoRelPath = $this->storeFile($request->file('plantilla')); // p.ej. "imports/archivo.csv"
            if (!empty($escenario->plantilla_subida)) {
                $this->deleteFile($escenario->plantilla_subida);
            }
            $data['plantilla_subida'] = $nuevoRelPath;

            // 2) reemplazar las filas del escenario en plantilla_a y recargar desde CSV
            DB::beginTransaction();
            try {
                $escenario->update($data);
                // se elimina la data anterior de plantilla
                $escenario->plantillasA()->delete();
                CopyImporterPlantillaA::importCsvToPlantillaA($nuevoRelPath, $escenario->id);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }

            // dispara tu evento al terminar
            // event(new ExcelImportCompleted($escenario->id));
        } else {
            $escenario->update($data);
        }

        // === imágenes (igual que antes) ===
        foreach ($imagenMapas as $tipo) {
            if ($request->hasFile($tipo)) {
                $imagenesAntiguas = $escenario->mapas()->where('tipo', $tipo)->get();
                foreach ($imagenesAntiguas as $imagen) {
                    $this->deleteFile($imagen->ruta, 'public');
                    $imagen->delete();
                }
                foreach ($request->file($tipo) as $file) {
                    $ruta = $this->storeFile($file, 'public');
                    $escenario->mapas()->create([
                        'tipo' => $tipo,
                        'ruta' => $ruta,
                    ]);
                }
            }
        }

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

    public function print(Request $request, Escenario $escenario)
    {
        $escenario->load(['formulario', 'mapas']);
        $data = $request->input('plantillasAList', []);
        $css = file_get_contents(public_path('build/assets/app-B3On5526.css')); // cambiar segun el nombre del css en public
        $html = view('pdf.escenario', compact('escenario', 'data', 'css'))->render();

        // return Browsershot::html($html)
        //     ->format('A4')
        //     ->landscape()
        //     ->margins(10, 10, 10, 10)
        //     ->waitUntilNetworkIdle()
        //     ->pdf(); // <-- Retorna el binario directamente
        $pdf = Pdf::loadView('pdf.escenario', compact('data', 'escenario', 'css'))->setPaper('a4', 'landscape');
        return $pdf->download('escenario.pdf');
    }
}
