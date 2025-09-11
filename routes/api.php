<?php

use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EscenarioController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'v1'], function () {
    //Route::group([ 'prefix' => 'auth' ], function () {
    //    Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
    //    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:api');
    //    Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh'])->middleware('auth:api');
    //    Route::post('me', [App\Http\Controllers\AuthController::class, 'me'])->middleware('auth:api');
    //});

    Route::resource('/escenarios', EscenarioController::class)->except('create', 'edit');
    Route::resource('/formularios', FormularioController::class)->only('index', 'show');

    Route::apiResource('/departamentos', DepartamentoController::class)->only('index');
    Route::apiResource('/usuarios', UserController::class);
});
