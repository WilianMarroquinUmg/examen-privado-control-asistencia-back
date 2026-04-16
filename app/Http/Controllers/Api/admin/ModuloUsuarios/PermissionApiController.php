<?php

namespace App\Http\Controllers\Api\admin\ModuloUsuarios;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\admin\ModuloUsuarios\CreatePermissionApiRequest;
use App\Http\Requests\Api\admin\ModuloUsuarios\UpdatePermissionApiRequest;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class PermissionApiController
 */
class PermissionApiController extends AppbaseController implements HasMiddleware
{

    /**
     * //     * @return array
     * //     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Permisos', only: ['index']),
            new Middleware('permission:Ver permisos', only: ['show', 'obtenerTodos']),
            new Middleware('permission:Crear permisos', only: ['store']),
            new Middleware('permission:Editar permisos', only: ['update']),
            new Middleware('permission:Eliminar permisos', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Permissions.
     * GET|HEAD /permissions
     */
    public function index(Request $request): JsonResponse
    {
        $permissions = QueryBuilder::for(Permission::class)
            ->with([])
            ->allowedFilters([
                'name',
                'subject',
            ])
            ->allowedSorts([
                'id',
                'name',
                'subject',
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->jsonPaginate(request('page.size', 10));

        return $this->sendResponse($permissions->toArray(), 'permissions recuperados con éxito.');
    }


    /**
     * Store a newly created Permission in storage.
     * POST /permissions
     */
    public function store(CreatePermissionApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['guard_name'] = Permission::GUARD_NAME_ACTUAL;

        $permissions = Permission::create($input);

        return $this->sendResponse($permissions->toArray(), 'Permission creado con éxito.');
    }


    /**
     * Display the specified Permission.
     * GET|HEAD /permissions/{id}
     */
    public function show(Permission $permission)
    {
        return $this->sendResponse($permission->toArray(), 'Permission recuperado con éxito.');
    }


    /**
     * Update the specified Permission in storage.
     * PUT/PATCH /permissions/{id}
     */
    public function update(UpdatePermissionApiRequest $request, $id): JsonResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->update($request->validated());
        return $this->sendResponse($permission, 'Permission actualizado con éxito.');
    }

    /**
     * Remove the specified Permission from storage.
     * DELETE /permissions/{id}
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $permission->delete();
        return $this->sendResponse(null, 'Permission eliminado con éxito.');
    }

    public function obtenerTodos()
    {
        $permissions = Permission::all();
        return $this->sendResponse($permissions->toArray(), 'Permisos recuperados con éxito.');
    }

}
