<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAsistenciaSesionApiRequest;
use App\Http\Requests\Api\UpdateAsistenciaSesionApiRequest;
use App\Models\AsistenciaSesion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AsistenciaSesionApiController
 */
class AsistenciaSesionApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Asistencia Sesiones', only: ['index']),
            new Middleware('permission:Ver Asistencia Sesiones', only: ['show']),
            new Middleware('permission:Crear Asistencia Sesiones', only: ['store']),
            new Middleware('permission:Editar Asistencia Sesiones', only: ['update']),
            new Middleware('permission:Eliminar Asistencia Sesiones', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Asistencia_sesiones.
     * GET|HEAD /asistencia_sesiones
     */
    public function index(Request $request): JsonResponse
    {
        $asistencia_sesiones = QueryBuilder::for(AsistenciaSesion::class)
            ->allowedFilters([
    'fecha',
    'estado',
    'espacio_id'
])
            ->allowedSorts([
    'fecha',
    'estado',
    'espacio_id'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($asistencia_sesiones, 'asistencia_sesiones recuperados con éxito.');
    }


    /**
     * Store a newly created AsistenciaSesion in storage.
     * POST /asistencia_sesiones
     */
    public function store(CreateAsistenciaSesionApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $asistencia_sesiones = AsistenciaSesion::create($input);

        return $this->sendResponse($asistencia_sesiones->toArray(), 'AsistenciaSesion creado con éxito.');
    }

    /**
     * Display the specified AsistenciaSesion.
     * GET|HEAD /asistencia_sesiones/{id}
     */
    public function show(AsistenciaSesion $asistenciasesion)
    {
        return $this->sendResponse($asistenciasesion->toArray(), 'AsistenciaSesion recuperado con éxito.');
    }

    /**
    * Update the specified AsistenciaSesion in storage.
    * PUT/PATCH /asistencia_sesiones/{id}
    */
    public function update(UpdateAsistenciaSesionApiRequest $request, $id): JsonResponse
    {
        $asistenciasesion = AsistenciaSesion::findOrFail($id);
        $asistenciasesion->update($request->validated());
        return $this->sendResponse($asistenciasesion, 'AsistenciaSesion actualizado con éxito.');
    }

    /**
    * Remove the specified AsistenciaSesion from storage.
    * DELETE /asistencia_sesiones/{id}
    */
    public function destroy(AsistenciaSesion $asistenciasesion): JsonResponse
    {
        $asistenciasesion->delete();
        return $this->sendResponse(null, 'AsistenciaSesion eliminado con éxito.');
    }
}
