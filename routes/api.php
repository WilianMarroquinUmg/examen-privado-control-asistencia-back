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

    Route::apiResource('asistencia-sesiones', App\Http\Controllers\Api\AsistenciaSesionApiController::class)
        ->parameters(['asistencia_sesiones' => 'asistenciasesion']);

    Route::apiResource('asistencia-configuraciones', App\Http\Controllers\Api\AsistenciaConfiguracionApiController::class)
        ->parameters(['asistencia_configuraciones' => 'asistenciaconfiguracion']);

    Route::apiResource('asistencia_sesion_tomas', App\Http\Controllers\Api\AsistenciaSesionTomaApiController::class)
        ->parameters(['asistencia_sesion_tomas' => 'asistenciasesiontoma']);


    Route::apiResource('asistencia-registros', App\Http\Controllers\Api\AsistenciaRegistroApiController::class)
        ->parameters(['asistencia_registros' => 'asistenciaregistro']);

});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});









