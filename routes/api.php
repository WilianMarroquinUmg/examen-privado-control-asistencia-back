<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->responseUser();
});

Route::middleware('auth:sanctum')->group(function () {

    require __DIR__.'/admin/api.php';

    Route::apiResource('facultades', App\Http\Controllers\Api\Pensum\FacultadApiController::class)
        ->parameters(['facultades' => 'facultad']);


    Route::apiResource('ciclos', App\Http\Controllers\Api\Pensum\CicloApiController::class)
        ->parameters(['ciclos' => 'ciclo']);


});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});











