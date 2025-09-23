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
        // $clientId = env('POWERBI_CLIENT_ID', '');
        // $clientSecret = env('POWERBI_CLIENT_SECRET', '');
        // $tenantId = env('POWERBI_TENANT_ID', '');
        // $workspaceId = env('POWERBI_WORKSPACE_ID', '');
        // $reportId = env('POWERBI_REPORT_ID', '');

        $escenario->formulario->plantilla === 'A' ?
            $data = PlantillaA::getByEscenario($escenario) :
            $data =  []; //$escenario->load('plantillasB');

        // Obtener Access Token desde Azure
        // $url = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";
        // $response = Http::asForm()->post($url, [
        //     'grant_type' => 'client_credentials',
        //     'client_id' => $clientId,
        //     'client_secret' => $clientSecret,
        //     'scope' => 'https://analysis.windows.net/powerbi/api/.default',
        // ]);

        // $accessToken = $response->json()['access_token'];

        // Obtener detalles del reporte
        // $reportUrl = "https://api.powerbi.com/v1.0/myorg/groups/{$workspaceId}/reports/{$reportId}";
        // $reportResponse = Http::withToken($accessToken)->get($reportUrl);

        // $reportData = $reportResponse->json();

        return response()->json([
            'escenario' => $escenario,
            'plantillas' => $data,
            // 'embedUrl' => $reportData['embedUrl'],
            // 'reportId' => $reportId,
            // 'accessToken' => $accessToken,
        ]);
    }

    public function store(EscenarioStoreRequest $request)
    {
        $data = $request->validated();
        $escenario = DB::transaction(function () use ($request, $data) {
            $data['plantilla_subida'] = $this->storeFile($request->file('plantilla'));
            $data['excel'] = $this->storeFile($request->file('excel'));
            $data['mapa_centro'] = $this->storeFile($request->file('mapa_centro'));
            $data['mapa_izquierda'] = $this->storeFile($request->file('mapa_izquierda'));
            // $escenario = Escenario::create($data);
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
        DB::transaction(function () use ($request, $escenario, $data) {

            if ($request->file('plantilla')) {
                $urlPlantillaSubida = $this->storeFile($request->file('plantilla'));
                $this->deleteFile($escenario->plantilla_subida);
                $escenario->plantillasA()->delete();
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
        });

        if ($request->file('plantilla')) {
            Excel::queueImport(new EscenarioImport($escenario->id), $request->file('plantilla'));
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

    public function downloadPlantilla(Request $request, Escenario $escenario)
    {
        $data = $request->input('data', []);
        return Excel::download(new PlantillaExport($data), "plantilla_$escenario->id.xlsx");
    }
}
