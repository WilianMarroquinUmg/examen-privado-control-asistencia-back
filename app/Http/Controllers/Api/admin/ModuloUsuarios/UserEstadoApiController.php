<?php

namespace App\Http\Controllers\Api\admin\ModuloUsuarios;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\admin\ModuloUsuarios\CreateUserEstadoApiRequest;
use App\Http\Requests\Api\admin\ModuloUsuarios\UpdateUserEstadoApiRequest;
use App\Models\UserEstado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class UserEstadoApiController
 */
class UserEstadoApiController extends AppbaseController implements HasMiddleware
{

    /**
     * //     * @return array
     * //     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Usuario Estados', only: ['index']),
            new Middleware('permission:Ver Usuario Estados', only: ['show']),
            new Middleware('permission:Crear Usuario Estados', only: ['store']),
            new Middleware('permission:Editar Usuario Estados', only: ['update']),
            new Middleware('permission:Eliminar Usuario Estados', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Users_estados.
     * GET|HEAD /users_estados
     */
    public function index(Request $request): JsonResponse
    {
        $users_estados = QueryBuilder::for(UserEstado::class)
            ->with([])
            ->allowedFilters([
                'nombre'
            ])
            ->allowedSorts([
                'nombre'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($users_estados->toArray(), 'Estados recuperados con éxito.');
    }


    /**
     * Store a newly created UserEstado in storage.
     * POST /users_estados
     */
    public function store(CreateUserEstadoApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $users_estados = UserEstado::create($input);

        return $this->sendResponse($users_estados->toArray(), 'UserEstado creado con éxito.');
    }


    /**
     * Display the specified UserEstado.
     * GET|HEAD /users_estados/{id}
     */
    public function show(UserEstado $estado)
    {
        return $this->sendResponse($estado->toArray(), 'UserEstado recuperado con éxito.');
    }


    /**
     * Update the specified UserEstado in storage.
     * PUT/PATCH /users_estados/{id}
     */
    public function update(UpdateUserEstadoApiRequest $request, $id): JsonResponse
    {
        $userestado = UserEstado::findOrFail($id);
        $userestado->update($request->validated());
        return $this->sendResponse($userestado, 'UserEstado actualizado con éxito.');
    }

    /**
     * Remove the specified UserEstado from storage.
     * DELETE /users_estados/{id}
     */
    public function destroy(UserEstado $estado): JsonResponse
    {
        $estado->delete();
        return $this->sendResponse(null, 'UserEstado eliminado con éxito.');
    }

}
