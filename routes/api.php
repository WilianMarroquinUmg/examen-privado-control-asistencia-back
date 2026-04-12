<?php

use App\Http\Controllers\Api\Pensum\CicloApiController;
use App\Http\Controllers\Api\Pensum\FacultadApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->responseUser();
});

Route::middleware('auth:sanctum')->group(function () {

    require __DIR__.'/admin/api.php';

    Route::post('facultad-asociar-ciclo', [FacultadApiController::class, 'asociarCiclo']);

    Route::post('facultad-des-asociar-ciclo', [FacultadApiController::class, 'desAsociarCiclo']);

    Route::apiResource('facultades', FacultadApiController::class)
        ->parameters(['facultades' => 'facultad']);

    Route::post('ciclos-asociar-cursos', [CicloApiController::class, 'asignarCurso']);

    Route::post('ciclos-des-asociar-cursos', [CicloApiController::class, 'desAsociarCurso']);


    Route::apiResource('ciclos', CicloApiController::class)
        ->parameters(['ciclos' => 'ciclo']);

    Route::apiResource('cursos', App\Http\Controllers\Api\Pensum\CursoApiController::class)
        ->parameters(['cursos' => 'curso']);


    Route::apiResource('trabajo-espacios', App\Http\Controllers\Api\EspacioTrabajo\TrabajoEspacioApiController::class)
        ->parameters(['trabajo_espacios' => 'trabajoespacio']);

});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});











