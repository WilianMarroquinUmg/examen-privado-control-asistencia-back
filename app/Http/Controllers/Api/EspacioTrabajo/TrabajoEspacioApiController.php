<?php

namespace App\Http\Controllers\Api\EspacioTrabajo;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\EspacioTrabajo\CreateTrabajoEspacioApiRequest;
use App\Http\Requests\Api\EspacioTrabajo\UpdateTrabajoEspacioApiRequest;
use App\Models\EspacioTrabajo\TrabajoEspacio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class TrabajoEspacioApiController
 */
class TrabajoEspacioApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Trabajo Espacios', only: ['index']),
            new Middleware('permission:Ver Trabajo Espacios', only: ['show']),
            new Middleware('permission:Crear Trabajo Espacios', only: ['store']),
            new Middleware('permission:Editar Trabajo Espacios', only: ['update']),
            new Middleware('permission:Eliminar Trabajo Espacios', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Trabajo_espacios.
     * GET|HEAD /trabajo_espacios
     */
    public function index(Request $request): JsonResponse
    {
        $trabajo_espacios = QueryBuilder::for(TrabajoEspacio::class)
            ->allowedFilters([
    'catedratico_id',
    'facultad_id',
    'ciclo_id',
    'curso_id'
])
            ->allowedSorts([
    'catedratico_id',
    'facultad_id',
    'ciclo_id',
    'curso_id'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($trabajo_espacios, 'trabajo_espacios recuperados con éxito.');
    }


    /**
     * Store a newly created TrabajoEspacio in storage.
     * POST /trabajo_espacios
     */
    public function store(CreateTrabajoEspacioApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $trabajo_espacios = TrabajoEspacio::create($input);

        return $this->sendResponse($trabajo_espacios->toArray(), 'TrabajoEspacio creado con éxito.');
    }

    /**
     * Display the specified TrabajoEspacio.
     * GET|HEAD /trabajo_espacios/{id}
     */
    public function show(TrabajoEspacio $trabajoespacio)
    {
        return $this->sendResponse($trabajoespacio->toArray(), 'TrabajoEspacio recuperado con éxito.');
    }

    /**
    * Update the specified TrabajoEspacio in storage.
    * PUT/PATCH /trabajo_espacios/{id}
    */
    public function update(UpdateTrabajoEspacioApiRequest $request, $id): JsonResponse
    {
        $trabajoespacio = TrabajoEspacio::findOrFail($id);
        $trabajoespacio->update($request->validated());
        return $this->sendResponse($trabajoespacio, 'TrabajoEspacio actualizado con éxito.');
    }

    /**
    * Remove the specified TrabajoEspacio from storage.
    * DELETE /trabajo_espacios/{id}
    */
    public function destroy(TrabajoEspacio $trabajoespacio): JsonResponse
    {
        $trabajoespacio->delete();
        return $this->sendResponse(null, 'TrabajoEspacio eliminado con éxito.');
    }
}
