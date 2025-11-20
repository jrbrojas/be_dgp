<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EscenarioController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/login', [AuthController::class, 'login']);

Route::get('v1/escenarios/show/{formulario}/pi', [EscenarioController::class, 'showPI']);
Route::post('v1/escenarios/{escenario}/download', [EscenarioController::class, 'download']);

Route::group(['middleware' => ['auth:api'], 'prefix' => 'v1'], function () {

    // escenarios
    Route::post('/escenarios-api', [EscenarioController::class, 'storeApi']);
    Route::apiResource('/escenarios', EscenarioController::class);

    // formularios
    Route::apiResource('/formularios', FormularioController::class)->only('index', 'show');

    //usuarios
    Route::apiResource('/usuarios', UsuarioController::class);
    Route::apiResource('/roles', RolController::class)->only('index');

    // auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
});
