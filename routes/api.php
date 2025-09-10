<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => 'v1'], function () {
    //Route::group([ 'prefix' => 'auth' ], function () {
    //    Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);
    //    Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:api');
    //    Route::post('refresh', [App\Http\Controllers\AuthController::class, 'refresh'])->middleware('auth:api');
    //    Route::post('me', [App\Http\Controllers\AuthController::class, 'me'])->middleware('auth:api');
    //});

    Route::resource('/escenario', \App\Http\Controllers\EscenarioController::class)->except('create', 'edit');
    Route::resource('/formulario', \App\Http\Controllers\FormularioController::class)->only('index', 'show');
});
