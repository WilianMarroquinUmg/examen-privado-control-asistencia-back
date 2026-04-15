<?php

namespace App\Http\Controllers\Api\Pensum;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\Pensum\CreateCursoApiRequest;
use App\Http\Requests\Api\Pensum\UpdateCursoApiRequest;
use App\Models\Pensum\Curso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class CursoApiController
 */
class CursoApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Cursos', only: ['index']),
            new Middleware('permission:Ver Cursos', only: ['show']),
            new Middleware('permission:Crear Cursos', only: ['store']),
            new Middleware('permission:Editar Cursos', only: ['update']),
            new Middleware('permission:Eliminar Cursos', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Cursos.
     * GET|HEAD /cursos
     */
    public function index(Request $request): JsonResponse
    {
        $cursos = QueryBuilder::for(Curso::class)
            ->allowedFilters([
                'nombre',
                'codigo',
                'identificacion_institucional',
                AllowedFilter::scope('sinAsociarACicloYFacultad', 'sinAsociarACicloYFacultad'),
                AllowedFilter::scope('asociadosACicloYFacultad', 'AsociadosACicloYFacultad'),
            ])
            ->allowedSorts([
                'nombre',
                'codigo',
                'identificacion_institucional'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($cursos, 'cursos recuperados con éxito.');
    }


    /**
     * Store a newly created Curso in storage.
     * POST /cursos
     */
    public function store(CreateCursoApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $cursos = Curso::create($input);

        return $this->sendResponse($cursos->toArray(), 'Curso creado con éxito.');
    }

    /**
     * Display the specified Curso.
     * GET|HEAD /cursos/{id}
     */
    public function show(Curso $curso)
    {
        return $this->sendResponse($curso->toArray(), 'Curso recuperado con éxito.');
    }

    /**
     * Update the specified Curso in storage.
     * PUT/PATCH /cursos/{id}
     */
    public function update(UpdateCursoApiRequest $request, $id): JsonResponse
    {
        $curso = Curso::findOrFail($id);
        $curso->update($request->validated());
        return $this->sendResponse($curso, 'Curso actualizado con éxito.');
    }

    /**
     * Remove the specified Curso from storage.
     * DELETE /cursos/{id}
     */
    public function destroy(Curso $curso): JsonResponse
    {
        $curso->delete();
        return $this->sendResponse(null, 'Curso eliminado con éxito.');
    }
}
