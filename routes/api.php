<?php

use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\EscenarioController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'v1'], function () {

    // escenarios
    Route::apiResource('/escenarios', EscenarioController::class);

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
});
