<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\DistritoController;
use App\Http\Controllers\EscenarioController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\ProvinciaController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'store'])->middleware('web')->name('login');

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1'], function () {
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
    Route::post('/logout', [AuthController::class, 'destroy']);

});
