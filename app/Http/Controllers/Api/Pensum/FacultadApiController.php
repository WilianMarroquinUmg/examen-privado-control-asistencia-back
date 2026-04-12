<?php

namespace App\Http\Controllers\Api\Pensum;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\Pensum\CreateFacultadApiRequest;
use App\Http\Requests\Api\Pensum\UpdateFacultadApiRequest;
use App\Models\Pensum\Facultad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class FacultadApiController
 */
class FacultadApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Facultades', only: ['index']),
            new Middleware('permission:Ver Facultades', only: ['show']),
            new Middleware('permission:Crear Facultades', only: ['store']),
            new Middleware('permission:Editar Facultades', only: ['update']),
            new Middleware('permission:Eliminar Facultades', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Facultades.
     * GET|HEAD /facultades
     */
    public function index(Request $request): JsonResponse
    {
        $facultades = QueryBuilder::for(Facultad::class)
            ->allowedFilters([
                'nombre',
                'codigo'
            ])
            ->allowedSorts([
                'nombre',
                'codigo'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($facultades, 'facultades recuperados con éxito.');
    }


    /**
     * Store a newly created Facultad in storage.
     * POST /facultades
     */
    public function store(CreateFacultadApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $facultades = Facultad::create($input);

        return $this->sendResponse($facultades->toArray(), 'Facultad creado con éxito.');
    }

    /**
     * Display the specified Facultad.
     * GET|HEAD /facultades/{id}
     */
    public function show(Facultad $facultad)
    {
        return $this->sendResponse($facultad->toArray(), 'Facultad recuperado con éxito.');
    }

    /**
     * Update the specified Facultad in storage.
     * PUT/PATCH /facultades/{id}
     */
    public function update(UpdateFacultadApiRequest $request, $id): JsonResponse
    {
        $facultad = Facultad::findOrFail($id);
        $facultad->update($request->validated());
        return $this->sendResponse($facultad, 'Facultad actualizado con éxito.');
    }

    /**
     * Remove the specified Facultad from storage.
     * DELETE /facultades/{id}
     */
    public function destroy(Facultad $facultad): JsonResponse
    {

        $facultad->ciclos()->detach();

        $facultad->delete();
        return $this->sendResponse(null, 'Facultad eliminado con éxito.');
    }

    public function asociarCiclo(Request $request)
    {
        $request->validate([
            'facultad_id' => 'required|exists:facultades,id',
            'ciclo_id' => 'required|exists:ciclos,id',
        ]);

        $facultad = Facultad::findOrFail($request->input('facultad_id'));
        $cicloId = $request->input('ciclo_id');

        if ($facultad->ciclos()->where('ciclos.id', $cicloId)->exists()) {
            return $this->sendError('El ciclo ya está asociado a esta facultad.');
        }

        $facultad->ciclos()->attach($cicloId);

        return $this->sendResponse(null, 'Ciclo asociado a la facultad con éxito.');

    }

    public function desAsociarCiclo(Request $request)
    {
        $request->validate([
            'facultad_id' => 'required|exists:facultades,id',
            'ciclo_id' => 'required|exists:ciclos,id',
        ]);

        $facultad = Facultad::findOrFail($request->input('facultad_id'));
        $cicloId = $request->input('ciclo_id');

        if (!$facultad->ciclos()->where('ciclos.id', $cicloId)->exists()) {
            return $this->sendError('El ciclo no está asociado a esta facultad.');
        }

        $facultad->ciclos()->detach($cicloId);

        return $this->sendResponse(null, 'Ciclo desasociado de la facultad con éxito.');
    }
}
