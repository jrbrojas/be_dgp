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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
     *
     * Lista los escenarios registrados, permitiendo filtrar y consultar su información principal.
     *
     * @return \Illuminate\Http\JsonResponse
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
     *
     * Devuelve el detalle completo de un escenario específico para mostrarlo en una plantilla.
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Mostrar informacion de un escenario para la Plataforma Integrada
     *
     * Devuelve el detalle completo de un formulario específico para mostrarlo en el modelo estático.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showPI(Request $request, Formulario $formulario)
    {
        $escenario = Escenario::where('formulario_id', $formulario->id)->orderBy('id', 'desc')->first();
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
     *
     * Registra un nuevo escenario, validando credenciales y token, y adjuntando la plantilla, el Excel y las imágenes de mapas.
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Guardar un nuevo escenario mediante una api publica
     *
     * Permite que un sistema externo (como Python) cargue un escenario completo enviando archivos individuales por tipo de mapa y datos estructurados del formulario.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeApi(EscenarioStoreRequest $request)
    {
        $userAunteticated = Auth::guard('api')->user();
        // validamos si esta autenticado
        if (! $userAunteticated) {
            return response()->json([
                'message' => 'Token inválido o no enviado.',
            ], 401);
        }

        // validamos si el correo y contraseña sea correctos
        if (
            $request->email !== $userAunteticated->email ||
            ! Hash::check($request->password, $userAunteticated->password)
        ) {
            return response()->json([
                'message' => 'Usuario o contraseña no válidos para el token proporcionado.',
            ], 401);
        }

        // validamos el token enviado en el body con el token de autorizacion
        $headerToken = $request->bearerToken();
        $bodyToken   = $request->input('token');

        if ($bodyToken !== null && $bodyToken !== $headerToken) {
            return response()->json([
                'message' => 'El token proporcionado no coincide con el token de autorización.',
            ], 401);
        }

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
     *
     *Actualiza los datos de un escenario existente, incluyendo sus archivos asociados.
     *
     * @return \Illuminate\Http\JsonResponse
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
     * Elimina un escenario y limpia los archivos relacionados almacenados en el sistema.
     *
     * @return \Illuminate\Http\JsonResponse
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

    /**
     * Genera y descarga la presentación PowerPoint del escenario de riesgo.
     *
     * A partir de las plantillas asociadas al escenario, renderiza las cards en imágenes PNG mediante Browsershot, las inserta en una presentación
     * PPTX y devuelve el archivo para su descarga. El archivo temporal se elimina automáticamente después de ser enviado.
     *
     * @param  \Illuminate\Http\Request                           $request
     * @param  \App\Models\Escenario                              $escenario
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
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
}
