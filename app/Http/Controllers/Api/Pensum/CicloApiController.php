<?php

namespace App\Http\Controllers\Api\Pensum;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\Pensum\CreateCicloApiRequest;
use App\Http\Requests\Api\Pensum\UpdateCicloApiRequest;
use App\Models\Pensum\Ciclo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class CicloApiController
 */
class CicloApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Ciclos', only: ['index']),
            new Middleware('permission:Ver Ciclos', only: ['show']),
            new Middleware('permission:Crear Ciclos', only: ['store']),
            new Middleware('permission:Editar Ciclos', only: ['update']),
            new Middleware('permission:Eliminar Ciclos', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Ciclos.
     * GET|HEAD /ciclos
     */
    public function index(Request $request): JsonResponse
    {
        $ciclos = QueryBuilder::for(Ciclo::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'nombre',
                AllowedFilter::scope('asociadosAFacultad', 'asociadosAFacultad'),
                AllowedFilter::scope('sinAsociarAFacultad', 'sinAsociarAFacultad'),
            ])
            ->allowedSorts([
                'nombre'
            ])
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($ciclos, 'ciclos recuperados con éxito.');
    }


    /**
     * Store a newly created Ciclo in storage.
     * POST /ciclos
     */
    public function store(CreateCicloApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $ciclos = Ciclo::create($input);

        return $this->sendResponse($ciclos->toArray(), 'Ciclo creado con éxito.');
    }

    /**
     * Display the specified Ciclo.
     * GET|HEAD /ciclos/{id}
     */
    public function show(Ciclo $ciclo)
    {
        return $this->sendResponse($ciclo->toArray(), 'Ciclo recuperado con éxito.');
    }

    /**
     * Update the specified Ciclo in storage.
     * PUT/PATCH /ciclos/{id}
     */
    public function update(UpdateCicloApiRequest $request, $id): JsonResponse
    {
        $ciclo = Ciclo::findOrFail($id);
        $ciclo->update($request->validated());
        return $this->sendResponse($ciclo, 'Ciclo actualizado con éxito.');
    }

    /**
     * Remove the specified Ciclo from storage.
     * DELETE /ciclos/{id}
     */
    public function destroy(Ciclo $ciclo): JsonResponse
    {
        $ciclo->delete();
        return $this->sendResponse(null, 'Ciclo eliminado con éxito.');
    }

    public function asignarCurso(Request $request): JsonResponse
    {
        $request->validate([
            'ciclo_id' => 'required|exists:ciclos,id',
            'curso_id' => 'required|exists:cursos,id',
        ]);

        $ciclo = Ciclo::findOrFail($request->ciclo_id);

        $ciclo->cursos()->attach($request->curso_id);

        return $this->sendSuccess('Cursos asignados al ciclo con éxito.');
    }

        public function desAsociarCurso(Request $request): JsonResponse
        {
            $request->validate([
                'ciclo_id' => 'required|exists:ciclos,id',
                'curso_id' => 'required|exists:cursos,id',
            ]);

            $ciclo = Ciclo::findOrFail($request->ciclo_id);

            $ciclo->cursos()->detach($request->curso_id);

            return $this->sendSuccess('Cursos desasociados del ciclo con éxito.');
        }
}
