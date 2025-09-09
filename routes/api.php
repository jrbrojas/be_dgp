<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::resource('/entidad', \App\Http\Controllers\EntidadController::class);
    Route::resource('/departamentos', \App\Http\Controllers\DepartmentController::class)->only('index');
    Route::resource('/distritos', \App\Http\Controllers\DistritoController::class)->only('index');
    Route::resource('/provincias', \App\Http\Controllers\ProvinciaController::class)->only('index');
});
//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');
