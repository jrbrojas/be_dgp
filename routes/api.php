<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\EscenarioController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\PlantillaAController;
use App\Http\Controllers\PlantillaBController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/login', [AuthController::class, 'login']);

Route::get('v1/escenarios/show/{formulario}/pi', [EscenarioController::class, 'showPI']);

Route::group(['middleware' => ['auth:api'], 'prefix' => 'v1'], function () {

    // escenarios
    Route::get('escenarios/formularios', [EscenarioController::class, 'formulariosFull']);
    Route::apiResource('/escenarios', EscenarioController::class);
    Route::post('escenarios/{escenario}/plantilla/print', [EscenarioController::class, 'print']);
    Route::post('escenarios/{escenario}/plantilla/download', [EscenarioController::class, 'downloadPlantilla']);

    // formularios
    Route::apiResource('/formularios', FormularioController::class)->only('index', 'show');

    // departamentos
    Route::apiResource('/departamentos', DepartamentoController::class)->only('index');
    // provincias
    Route::apiResource('/provincias', ProvinciaController::class)->only('index');
    // distritos
    Route::apiResource('/distritos', DistritoController::class)->only('index');

    //usuarios
    Route::apiResource('/usuarios', UserController::class);
    Route::apiResource('/roles', RoleController::class)->only('index');

    // auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);
});
