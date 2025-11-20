<?php

namespace App\Http\Controllers;

use App\Exports\PlantillaExport;
use App\Http\Requests\EscenarioStoreRequest;
use Illuminate\Support\Str;
use App\Models\Escenario;
use App\Models\Formulario;
use App\Models\Mapa;
use App\Models\Renat\VistaInstrumentos;
use App\Support\CopyImporterPlantilla;
use App\View\Components\FormatNombreArray;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use Spatie\Browsershot\Browsershot;

class EscenarioController extends Controller
{
    /**
     * Listar los escenarios
     */
    public function index(Request $request)
    {
        $escenarios = Escenario::with('formulario')
            ->search($request['query'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'list' => $escenarios,
            'total' => $escenarios->count(),
        ]);
    }

    /**
     * Mostrar informacion de un escenario
     */
    public function show(Escenario $escenario)
    {
        $escenario->excel_adjunto = asset('storage/' . $escenario->excel_adjunto);
        $data = Escenario::getByFormulario($escenario);
        $instrumentos = VistaInstrumentos::instrumentosPorNivel($data);

        return response()->json([
            'escenario' => $escenario->load(['formulario', 'mapas']),
            'plantillas' => $data,
            'instrumentos' => $instrumentos,
        ]);
    }

    /**
     * Mostrar informacion de un escenario para la plataforma integrada
     */
    public function showPI(Request $request)
    {
        $escenario = Escenario::where('formulario_id', $request->formulario)->orderBy('id', 'desc')->first();
        $escenario->excel_adjunto = asset('storage/' . $escenario->excel_adjunto);
        $data = $escenario ? Escenario::getByFormulario($escenario) : [];
        $instrumentos = VistaInstrumentos::instrumentosPorNivel($data);

        return response()->json([
            'escenario' => $escenario ? $escenario->load(['formulario', 'mapas']) : null,
            'plantillas' => $data,
            'instrumentos' => $instrumentos,
        ]);
    }

    /**
     * Guardar un nuevo escenario
     */
    public function store(EscenarioStoreRequest $request)
    {
        $camposFile = [
            'imagen_derecho_inu',
            'imagen_derecho_mm',
            'imagen_derecho_bt',
            'imagen_centro_inu',
            'imagen_centro_mm',
            'imagen_centro_bt',
            'imagen_centro_inc',
            'imagen_centro_sismo',
            'imagen_centro_tsunami',
            'imagen_centro_glaciar',
            'imagen_izquierdo_inu',
            'imagen_izquierdo_mm',
            'imagen_izquierdo_bt',
            'imagen_izquierdo_inc',
            'imagen_izquierdo_sismo',
            'imagen_izquierdo_tsunami',
            'imagen_izquierdo_glaciar',
            'imagen_izquierdo_superior_inu',
            'imagen_izquierdo_superior_mm',
            'imagen_izquierdo_superior_bt',
            'imagen_izquierdo_inferior_inu',
            'imagen_izquierdo_inferior_mm',
            'imagen_izquierdo_inferior_bt',
        ];

        $data = $request->validated();
        $escenario = DB::transaction(function () use ($request, $data, $camposFile) {
            $data['plantilla_subida'] = $this->storeFile($request->file('plantilla'));
            $data['excel_adjunto'] = $this->storeFile($request->file('excel_adjunto'), 'public');
            $escenarioData = Escenario::create($data);

            foreach ($camposFile as $tipo) {
                if ($request->hasFile($tipo)) {
                    $ruta = $this->storeFile($request->file($tipo), 'public');
                    Mapa::create([
                        'escenario_id' => $escenarioData->id, // << Agrega un campo "tipo" para identificar el mapa
                        'tipo' => $tipo, // << Agrega un campo "tipo" para identificar el mapa
                        'ruta' => $ruta,  // Asegúrate de tener un campo "ruta" en la tabla
                    ]);
                }
            }

            return $escenarioData;
        });

        // procesar la plantilla
        if ($request->file('plantilla')) {
            $storedRelPath = $escenario->plantilla_subida; // lo que retorna storeFile (p.ej. "imports/xxxx.csv|xlsx")
            $escenario->formulario->plantilla === 'A' ?
                CopyImporterPlantilla::importCsvToPlantillaA($storedRelPath, $escenario->id) :
                CopyImporterPlantilla::importCsvToPlantillaB($storedRelPath, $escenario->id);
        }

        return response()->json(['message' => 'Escenario creado correctamente!']);
    }

    /**
     * Actualizar un escenario
     */
    public function update(EscenarioStoreRequest $request, Escenario $escenario)
    {
        Log::info($request->all());
        $camposFile = [
            'imagen_derecho_inu',
            'imagen_derecho_mm',
            'imagen_derecho_bt',
            'imagen_centro_inu',
            'imagen_centro_mm',
            'imagen_centro_bt',
            'imagen_centro_inc',
            'imagen_centro_sismo',
            'imagen_centro_tsunami',
            'imagen_centro_glaciar',
            'imagen_izquierdo_inu',
            'imagen_izquierdo_mm',
            'imagen_izquierdo_bt',
            'imagen_izquierdo_inc',
            'imagen_izquierdo_sismo',
            'imagen_izquierdo_tsunami',
            'imagen_izquierdo_glaciar',
            'imagen_izquierdo_superior_inu',
            'imagen_izquierdo_superior_mm',
            'imagen_izquierdo_superior_bt',
            'imagen_izquierdo_inferior_inu',
            'imagen_izquierdo_inferior_mm',
            'imagen_izquierdo_inferior_bt',
        ];

        $data = $request->validated();

        if ($request->file('excel_adjunto')) {
            $urlExcel = $this->storeFile($request->file('excel_adjunto'), 'public');
            if (!empty($escenario->excel_adjunto)) {
                $this->deleteFile($escenario->excel_adjunto);
            }
            $data['excel_adjunto'] = $urlExcel;
        }

        if ($request->file('plantilla')) {

            // 1) guardar nuevo archivo y eliminar el anterior
            $nuevoRelPath = $this->storeFile($request->file('plantilla'));
            if (!empty($escenario->plantilla_subida)) {
                $this->deleteFile($escenario->plantilla_subida);
            }
            $data['plantilla_subida'] = $nuevoRelPath;

            DB::beginTransaction();
            try {
                $escenario->update($data);
                // se elimina la data anterior de plantilla
                if ($escenario->formulario->plantilla === 'A') {
                    $escenario->plantillasA()->delete();
                    CopyImporterPlantilla::importCsvToPlantillaA($nuevoRelPath, $escenario->id);
                } else {
                    $escenario->plantillasB()->delete();
                    CopyImporterPlantilla::importCsvToPlantillaB($nuevoRelPath, $escenario->id);
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        } else {
            $escenario->update($data);
        }

        foreach ($camposFile as $tipo) {

            if ($request->hasFile($tipo)) {
                $imagenAntigua = $escenario->mapas()->where('tipo', $tipo)->first();
                if ($imagenAntigua) {
                    $this->deleteFile($imagenAntigua->ruta, 'public');
                    $imagenAntigua->delete();
                }

                $ruta = $this->storeFile($request->file($tipo), 'public');
                $escenario->mapas()->create([
                    'tipo' => $tipo,
                    'ruta' => $ruta,
                ]);
            }
        }

        return response()->json(['message' => 'Escenario actualizado correctamente!']);
    }

    /**
     * Eliminar un escenario
     *
     * @response array{"message":"Escenario eliminado exitosamente!"}
     */
    public function destroy(Request $request, Escenario $escenario)
    {
        $camposFile = [
            'imagen_derecho_inu',
            'imagen_derecho_mm',
            'imagen_derecho_bt',
            'imagen_centro_inu',
            'imagen_centro_mm',
            'imagen_centro_bt',
            'imagen_centro_inc',
            'imagen_centro_sismo',
            'imagen_centro_tsunami',
            'imagen_centro_glaciar',
            'imagen_izquierdo_inu',
            'imagen_izquierdo_mm',
            'imagen_izquierdo_bt',
            'imagen_izquierdo_inc',
            'imagen_izquierdo_sismo',
            'imagen_izquierdo_tsunami',
            'imagen_izquierdo_glaciar',
            'imagen_izquierdo_superior_inu',
            'imagen_izquierdo_superior_mm',
            'imagen_izquierdo_superior_bt',
            'imagen_izquierdo_inferior_inu',
            'imagen_izquierdo_inferior_mm',
            'imagen_izquierdo_inferior_bt',
        ];

        return DB::transaction(function () use ($escenario, $camposFile) {
            foreach ($camposFile as $campo) {
                if (!empty($escenario->{$campo})) {
                    $this->deleteFile($escenario->{$campo});
                }
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

        $originalName = $file->getClientOriginalName();

        return Storage::disk($acceso)->putFileAs('escenarios', $file, $originalName);
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

    public function excel(Request $request, Escenario $escenario)
    {
        $path = $escenario->excel_adjunto;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fileName = 'escenario-riesgo-' . $escenario->id . '.xlsx';

        return Storage::disk('public')->download($path, $fileName);
    }

    /**
     * Descargar excel
     */
    public function download(Request $request, Escenario $escenario)
    {
        $formulario = [
            '1' => 'ppt.lluviasAvisoMeteorologico',
            '2' => 'ppt.lluviasAvisoTrimestral',
            '3' => 'ppt.lluviasInformacionClimatica',
            '4' => 'ppt.bajasTempAvisoMeteorologico',
            '5' => 'ppt.bajasTempAvisoTrimestral',
            '6' => 'ppt.bajasTempInformacionClimatica',
            '7' => 'ppt.incForestalesNacionales',
            '8' => 'ppt.incForestalesRegionales',
            '9' => 'ppt.sismosTsunamiNacional',
        ];

        $escenario->load('formulario');
        $plantillas = $request->plantillasAList ?? $request->data;

        // 1) Crear presentación
        $ppt = new PhpPresentation();
        $layout = new DocumentLayout();
        $layout->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9, true);
        $ppt->setLayout($layout);

        // se usa para produccion
        $chromeUserDir  = storage_path('app/chrome-user');
        $chromeDataDir  = storage_path('app/chrome-data');
        $chromeCacheDir = storage_path('app/chrome-cache');

        // Opcional: decirle a Chrome/Puppeteer que use storage/ como "home"
        putenv('HOME=' . storage_path('app'));
        putenv('XDG_CONFIG_HOME=' . storage_path('app'));
        putenv('XDG_CACHE_HOME=' . storage_path('app'));

        if ($escenario->formulario_id == 3) {

            $html = view($formulario[$escenario->formulario_id], compact('escenario', 'plantillas'))->render();

            $pngName = 'card-' . Str::uuid() . '.png';
            $pngPath = storage_path("app/tmp/{$pngName}");
            @mkdir(dirname($pngPath), 0775, true);

            // se usa en produccion
            Browsershot::html($html)
                ->setChromePath('/usr/bin/chromium')
                ->setNodeBinary('/usr/bin/node')
                ->setNpmBinary('/usr/bin/npm')
                ->windowSize(1280, 720)
                ->addChromiumArguments([
                    '--no-sandbox',
                    '--disable-gpu',
                    '--disable-dev-shm-usage',
                ])
                ->deviceScaleFactor(2)
                ->timeout(30)
                ->save($pngPath);

            // para usar localmente (dev)
            // Browsershot::html($html)
            //     ->select('#capture')
            //     ->windowSize(1280, 720)
            //     ->deviceScaleFactor(2)
            //     ->timeout(30)
            //     ->save($pngPath);

            // Crear una diapositiva (solo la primera usa getActiveSlide())
            $slide = $ppt->getActiveSlide();
            $shape = $slide->createDrawingShape();
            $shape->setName('Card');
            $shape->setDescription('Card exportado');
            $shape->setPath($pngPath);
            $shape->setResizeProportional(true);

            $slideW = 960;
            $slideH = 540;
            $shape->setHeight(520);
            $imgW = $shape->getWidth();
            $imgH = $shape->getHeight();
            $shape->setOffsetX(($slideW - $imgW) / 2);
            $shape->setOffsetY(($slideH - $imgH) / 2);

            // 5) Guardar PPTX
            $pptxName = 'escenario_riesgo_' . $escenario->id . '_' . date('Ymd_His') . '.pptx';
            $pptxPath = storage_path("app/tmp/{$pptxName}");
            $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
            $writer->save($pptxPath);
        } else {

            // se recorre por cada tipo que haya (inundaciones - movimiento_masa)
            foreach ($plantillas as $tipo => $data) {

                $html = view($formulario[$escenario->formulario_id], compact('escenario', 'data', 'tipo'))->render();

                $pngName = 'card-' . Str::uuid() . '.png';
                $pngPath = storage_path("app/tmp/{$pngName}");
                @mkdir(dirname($pngPath), 0775, true);

                // se usa en produccion
                Browsershot::html($html)
                    ->setChromePath('/usr/bin/chromium')
                    ->setNodeBinary('/usr/bin/node')
                    ->setNpmBinary('/usr/bin/npm')
                    ->addChromiumArguments([
                        '--no-sandbox',
                        '--disable-gpu',
                        '--disable-dev-shm-usage',
                    ])
                    ->windowSize(1280, 720)
                    ->deviceScaleFactor(2)
                    ->timeout(30)
                    ->save($pngPath);


                // para usar localmente (dev)
                // Browsershot::html($html)
                //     ->select('#capture')
                //     ->windowSize(1280, 720)
                //     ->deviceScaleFactor(2)
                //     ->timeout(30)
                //     ->save($pngPath);

                // Crear una diapositiva (solo la primera usa getActiveSlide())
                $slide = ($tipo === 'inundaciones')
                    ? $ppt->getActiveSlide()
                    : $ppt->createSlide();

                $shape = $slide->createDrawingShape();
                $shape->setName('Card');
                $shape->setDescription('Card exportado');
                $shape->setPath($pngPath);
                $shape->setResizeProportional(true);

                $slideW = 960;
                $slideH = 540;
                $shape->setHeight(520);
                $imgW = $shape->getWidth();
                $imgH = $shape->getHeight();
                $shape->setOffsetX(($slideW - $imgW) / 2);
                $shape->setOffsetY(($slideH - $imgH) / 2);
            }

            // 5) Guardar PPTX
            $pptxName = 'escenario_riesgo_' . $escenario->id . '_' . date('Ymd_His') . '.pptx';
            $pptxPath = storage_path("app/tmp/{$pptxName}");
            $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
            $writer->save($pptxPath);
        }


        return response()->download($pptxPath, $pptxName)->deleteFileAfterSend(true);
    }

    public function download2(Request $request, Escenario $escenario)
    {
        $formulario = [
            '1' => 'ppt.lluviasAvisoMeteorologico',
            '2' => 'ppt.lluviasAvisoTrimestral',
            '3' => 'ppt.lluviasInformacionClimatica',
            '4' => 'ppt.bajasTempAvisoMeteorologico',
            '5' => 'ppt.bajasTempAvisoTrimestral',
            '6' => 'ppt.bajasTempInformacionClimatica',
            '7' => 'ppt.incForestalesNacionales',
            '8' => 'ppt.incForestalesRegionales',
            '9' => 'ppt.sismosTsunamiNacional',
        ];

        $escenario->load('formulario');
        $plantillas = $request->plantillasAList ?? $request->data;

        // 1) Crear presentación
        $ppt = new PhpPresentation();
        $layout = new DocumentLayout();
        $layout->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9, true);
        $ppt->setLayout($layout);

        if ($escenario->formulario_id == 3) {
            $chromeUserDir  = storage_path('app/chrome-user');
            $chromeDataDir  = storage_path('app/chrome-data');
            $chromeCacheDir = storage_path('app/chrome-cache');

            // Opcional: decirle a Chrome/Puppeteer que use storage/ como "home"
            putenv('HOME=' . storage_path('app'));
            putenv('XDG_CONFIG_HOME=' . storage_path('app'));
            putenv('XDG_CACHE_HOME=' . storage_path('app'));

            $html = view($formulario[$escenario->formulario_id], compact('escenario', 'plantillas'))->render();

            $pngName = 'card-' . Str::uuid() . '.png';
            $pngPath = storage_path("app/tmp/{$pngName}");
            @mkdir(dirname($pngPath), 0775, true);

            Browsershot::html($html)
                ->setChromePath('/usr/bin/chromium')
                ->setNodeBinary('/usr/bin/node')
                ->setNpmBinary('/usr/bin/npm')
                ->windowSize(1280, 720)
                ->addChromiumArguments([
                    '--no-sandbox',
                    '--disable-gpu',
                    '--disable-dev-shm-usage',
                ])
                ->deviceScaleFactor(3)
                ->timeout(120)
                ->save($pngPath);

            // se usa en produccion
            // Browsershot::html($html)
            //     ->setChromePath('/usr/bin/chromium')
            //     ->setNodeBinary('/usr/bin/node')
            //     ->setNpmBinary('/usr/bin/npm')
            //     ->windowSize(1280, 720)
            //     ->addChromiumArguments([
            //         '--no-sandbox',
            //         '--disable-setuid-sandbox',
            //         '--disable-gpu',
            //         '--disable-dev-shm-usage',
            //         '--disable-crash-reporter',
            //         '--noerrdialogs',
            //         '--disable-extensions',
            //         '--disable-features=TranslateUI',
            //         '--user-data-dir=' . $chromeUserDir,
            //         '--data-path='     . $chromeDataDir,
            //         '--disk-cache-dir='. $chromeCacheDir,
            //     ])
            //     ->deviceScaleFactor(3)
            //     ->select('#capture')
            //     ->timeout(120)
            //     ->save($pngPath);

            // para usar localmente (dev)
            // Browsershot::html($html)
            //     ->select('#capture')
            //     ->windowSize(1280, 720)
            //     ->deviceScaleFactor(3)
            //     ->timeout(60)
            //     ->save($pngPath);

            // Crear una diapositiva (solo la primera usa getActiveSlide())
            $slide = $ppt->getActiveSlide();
            $shape = $slide->createDrawingShape();
            $shape->setName('Card');
            $shape->setDescription('Card exportado');
            $shape->setPath($pngPath);
            $shape->setResizeProportional(true);

            $slideW = 960;
            $slideH = 540;
            $shape->setHeight(520);
            $imgW = $shape->getWidth();
            $imgH = $shape->getHeight();
            $shape->setOffsetX(($slideW - $imgW) / 2);
            $shape->setOffsetY(($slideH - $imgH) / 2);


            // 5) Guardar PPTX
            $pptxName = 'escenario_riesgo_' . $escenario->id . '_' . date('Ymd_His') . '.pptx';
            $pptxPath = storage_path("app/tmp/{$pptxName}");
            $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
            $writer->save($pptxPath);

            // Limpieza del PNG al finalizar la descarga
            return response()->download($pptxPath, $pptxName)->deleteFileAfterSend(true);
        } else {

            // se recorre por cada tipo que haya (inundaciones - movimiento_masa)
            foreach ($plantillas as $tipo => $data) {

                $chromeUserDir  = storage_path('app/chrome-user');
                $chromeDataDir  = storage_path('app/chrome-data');
                $chromeCacheDir = storage_path('app/chrome-cache');

                // Opcional: decirle a Chrome/Puppeteer que use storage/ como "home"
                putenv('HOME=' . storage_path('app'));
                putenv('XDG_CONFIG_HOME=' . storage_path('app'));
                putenv('XDG_CACHE_HOME=' . storage_path('app'));

                $html = view($formulario[$escenario->formulario_id], compact('escenario', 'data', 'tipo'))->render();

                $pngName = 'card-' . Str::uuid() . '.png';
                $pngPath = storage_path("app/tmp/{$pngName}");
                @mkdir(dirname($pngPath), 0775, true);

                Browsershot::html($html)
                    ->setChromePath('/usr/bin/chromium')
                    ->setNodeBinary('/usr/bin/node')
                    ->setNpmBinary('/usr/bin/npm')
                    ->addChromiumArguments([
                        '--no-sandbox',
                        '--disable-gpu',
                        '--disable-dev-shm-usage',
                    ])
                    ->windowSize(1280, 720)
                    ->deviceScaleFactor(3)
                    ->timeout(120)
                    ->save($pngPath);

                // se usa en produccion
                // Browsershot::html($html)
                //     ->setChromePath('/usr/bin/chromium')
                //     ->setNodeBinary('/usr/bin/node')
                //     ->setNpmBinary('/usr/bin/npm')
                //     ->windowSize(1280, 720)
                //     ->addChromiumArguments([
                //         '--no-sandbox',
                //         '--disable-setuid-sandbox',
                //         '--disable-gpu',
                //         '--disable-dev-shm-usage',
                //         '--disable-crash-reporter',
                //         '--noerrdialogs',
                //         '--disable-extensions',
                //         '--disable-features=TranslateUI',
                //         '--user-data-dir=' . $chromeUserDir,
                //         '--data-path='     . $chromeDataDir,
                //         '--disk-cache-dir='. $chromeCacheDir,
                //     ])
                //     ->deviceScaleFactor(3)
                //     ->select('#capture')
                //     ->timeout(120)
                //     ->save($pngPath);

                // para usar localmente (dev)
                // Browsershot::html($html)
                //     ->select('#capture')
                //     ->windowSize(1280, 720)
                //     ->deviceScaleFactor(3)
                //     ->timeout(60)
                //     ->save($pngPath);

                // Crear una diapositiva (solo la primera usa getActiveSlide())
                $slide = ($tipo === 'inundaciones')
                    ? $ppt->getActiveSlide()
                    : $ppt->createSlide();

                $shape = $slide->createDrawingShape();
                $shape->setName('Card');
                $shape->setDescription('Card exportado');
                $shape->setPath($pngPath);
                $shape->setResizeProportional(true);

                $slideW = 960;
                $slideH = 540;
                $shape->setHeight(520);
                $imgW = $shape->getWidth();
                $imgH = $shape->getHeight();
                $shape->setOffsetX(($slideW - $imgW) / 2);
                $shape->setOffsetY(($slideH - $imgH) / 2);
            }

            // 5) Guardar PPTX
            $pptxName = 'escenario_riesgo_' . $escenario->id . '_' . date('Ymd_His') . '.pptx';
            $pptxPath = storage_path("app/tmp/{$pptxName}");
            $writer = IOFactory::createWriter($ppt, 'PowerPoint2007');
            $writer->save($pptxPath);

            // Limpieza del PNG al finalizar la descarga
            return response()->download($pptxPath, $pptxName)->deleteFileAfterSend(true);
        }
    }
}
