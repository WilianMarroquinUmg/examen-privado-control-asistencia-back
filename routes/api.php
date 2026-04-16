<?php

use App\Http\Controllers\Api\ExportableDataTableApiController;
use App\Http\Controllers\Api\Pensum\CicloApiController;
use App\Http\Controllers\Api\Pensum\FacultadApiController;
use App\Http\Controllers\Api\PerfilBiometricoApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user()->responseUser();
});

Route::middleware('auth:sanctum')->group(function () {

    require __DIR__.'/admin/api.php';

    Route::get('perfil-biometrico', [PerfilBiometricoApiController::class, 'show']);
    Route::post('perfil-biometrico', [PerfilBiometricoApiController::class, 'store']);

    Route::prefix('admin-perfiles-biometricos')->group(function () {
        Route::get('/{id}', [PerfilBiometricoApiController::class, 'showInfoAlumno']);
        Route::post('/{id}/certificar', [PerfilBiometricoApiController::class, 'certificar']);
        Route::post('/{id}/rechazar', [PerfilBiometricoApiController::class, 'rechazar']);
    });

    Route::prefix('exportar')->group(function () {
        Route::post('/excel', [ExportableDataTableApiController::class, 'exportarExcel']);

        Route::post('/pdf', [ExportableDataTableApiController::class, 'exportarPdf']);

        Route::get('/publico/wordToPdf', [ExportableDataTableApiController::class, 'wordToPdf'])->withoutMiddleware('auth:sanctum');

        Route::post('status-asistencia', [ExportableDataTableApiController::class, 'exportarStatusAsistencia']);
    });

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

    Route::get('trabajo-espacios-get-asistencias/{espacioId}', [App\Http\Controllers\Api\EspacioTrabajo\TrabajoEspacioApiController::class, 'getListadoAsistencia']);

    Route::apiResource('trabajo-espacios', App\Http\Controllers\Api\EspacioTrabajo\TrabajoEspacioApiController::class)
        ->parameters(['trabajo_espacios' => 'trabajoespacio']);

    Route::apiResource('asistencia-sesiones', App\Http\Controllers\Api\AsistenciaSesionApiController::class)
        ->parameters(['asistencia_sesiones' => 'asistenciasesion']);

    Route::apiResource('asistencia-configuraciones', App\Http\Controllers\Api\AsistenciaConfiguracionApiController::class)
        ->parameters(['asistencia_configuraciones' => 'asistenciaconfiguracion']);

    Route::apiResource('asistencia-sesion-tomas', App\Http\Controllers\Api\AsistenciaSesionTomaApiController::class)
        ->parameters(['asistencia_sesion_tomas' => 'asistenciasesiontoma']);

    Route::apiResource('asistencia-registros', App\Http\Controllers\Api\AsistenciaRegistroApiController::class)
        ->parameters(['asistencia_registros' => 'asistenciaregistro']);

    Route::post('solicitar-liveness-session', [App\Http\Controllers\Api\AsistenciaSesionTomaApiController::class, 'solicitarLiveness']);

});

require __DIR__.'/auth.php';

Route::prefix('libres')->group(function () {

    require __DIR__.'/admin/Configuraciones/api_libres.php';

});









