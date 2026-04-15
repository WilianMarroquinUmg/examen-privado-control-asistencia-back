<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAsistenciaConfiguracionApiRequest;
use App\Http\Requests\Api\UpdateAsistenciaConfiguracionApiRequest;
use App\Models\AsistenciaConfiguracion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AsistenciaConfiguracionApiController
 */
class AsistenciaConfiguracionApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Asistencia Configuraciones', only: ['index']),
            new Middleware('permission:Ver Asistencia Configuraciones', only: ['show']),
            new Middleware('permission:Crear Asistencia Configuraciones', only: ['store']),
            new Middleware('permission:Editar Asistencia Configuraciones', only: ['update']),
            new Middleware('permission:Eliminar Asistencia Configuraciones', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Asistencia_configuraciones.
     * GET|HEAD /asistencia_configuraciones
     */
    public function index(Request $request): JsonResponse
    {
        $asistencia_configuraciones = QueryBuilder::for(AsistenciaConfiguracion::class)
            ->allowedFilters([
    'requiere_ubicacion',
    'requiere_prueba_vida',
    'requiere_codigo_otp',
    'cantidad_tomas_requeridas',
    'minutos_tolerancia',
    'catedratico_id',
    'espacio_id'
])
            ->allowedSorts([
    'requiere_ubicacion',
    'requiere_prueba_vida',
    'requiere_codigo_otp',
    'cantidad_tomas_requeridas',
    'minutos_tolerancia',
    'catedratico_id',
    'espacio_id'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($asistencia_configuraciones, 'asistencia_configuraciones recuperados con éxito.');
    }


    /**
     * Store a newly created AsistenciaConfiguracion in storage.
     * POST /asistencia_configuraciones
     */
    public function store(CreateAsistenciaConfiguracionApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $asistencia_configuraciones = AsistenciaConfiguracion::create($input);

        return $this->sendResponse($asistencia_configuraciones->toArray(), 'AsistenciaConfiguracion creado con éxito.');
    }

    /**
     * Display the specified AsistenciaConfiguracion.
     * GET|HEAD /asistencia_configuraciones/{id}
     */
    public function show(AsistenciaConfiguracion $asistenciaconfiguracion)
    {
        return $this->sendResponse($asistenciaconfiguracion->toArray(), 'AsistenciaConfiguracion recuperado con éxito.');
    }

    /**
    * Update the specified AsistenciaConfiguracion in storage.
    * PUT/PATCH /asistencia_configuraciones/{id}
    */
    public function update(UpdateAsistenciaConfiguracionApiRequest $request, $id): JsonResponse
    {
        $asistenciaconfiguracion = AsistenciaConfiguracion::findOrFail($id);
        $asistenciaconfiguracion->update($request->validated());
        return $this->sendResponse($asistenciaconfiguracion, 'AsistenciaConfiguracion actualizado con éxito.');
    }

    /**
    * Remove the specified AsistenciaConfiguracion from storage.
    * DELETE /asistencia_configuraciones/{id}
    */
    public function destroy(AsistenciaConfiguracion $asistenciaconfiguracion): JsonResponse
    {
        $asistenciaconfiguracion->delete();
        return $this->sendResponse(null, 'AsistenciaConfiguracion eliminado con éxito.');
    }
}
