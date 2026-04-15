<?php

namespace App\Http\Controllers\Api\admin\ModuloUsuarios;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\admin\ModuloUsuarios\CreateRolApiRequest;
use App\Http\Requests\Api\admin\ModuloUsuarios\UpdateRolApiRequest;
use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class RolApiController
 */
class RolApiController extends AppbaseController implements HasMiddleware
{

    /**
     * //     * @return array
     * //     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Roles', only: ['index']),
            new Middleware('permission:Ver Roles', only: ['index', 'show']),
            new Middleware('permission:Crear Roles', only: ['store']),
            new Middleware('permission:Editar Roles', only: ['update']),
            new Middleware('permission:Eliminar Roles', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Roles.
     * GET|HEAD /roles
     */
    public function index(Request $request): JsonResponse
    {
        $roles = QueryBuilder::for(Rol::class)
            ->allowedIncludes([
                'permissions',
            ])
            ->allowedFilters([
                'name',
                'guard_name'
            ])
            ->allowedSorts([
                'id',
                'name',
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($roles->toArray(), 'roles recuperados con éxito.');
    }


    /**
     * Store a newly created Rol in storage.
     * POST /roles
     */
    public function store(CreateRolApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['guard_name'] = Rol::GUARD_NAME_ACTUAL;

        $rol = Rol::create($input);

        if($input['permisos']) {

            $rol->syncPermissions($input['permisos']);

        }

        return $this->sendResponse($rol->toArray(), 'Rol creado con éxito.');
    }


    /**
     * Display the specified Rol.
     * GET|HEAD /roles/{id}
     */
    public function show(Rol $rol)
    {
        return $this->sendResponse($rol->toArray(), 'Rol recuperado con éxito.');
    }


    /**
     * Update the specified Rol in storage.
     * PUT/PATCH /roles/{id}
     */
    public function update(UpdateRolApiRequest $request, $id): JsonResponse
    {
        $rol = Rol::findOrFail($id);
        $rol->update($request->validated());

        if($request->get('permisos')) {

            $rol->syncPermissions($request->get('permisos'));

        }

        return $this->sendResponse($rol, 'Rol actualizado con éxito.');
    }

    /**
     * Remove the specified Rol from storage.
     * DELETE /roles/{id}
     */
    public function destroy(Rol $rol): JsonResponse
    {
        $rol->delete();
        return $this->sendResponse(null, 'Rol eliminado con éxito.');
    }

    public function asignarPermisosARol(Rol $rol, Request $request)
    {

        $rol->syncPermissions($request->get('permisos', []));

        return $this->sendResponse(null, 'Permisos asignados con éxito.');

    }

    public function quitarPermisosARol(Rol $rol, Request $request)
    {

        $rol->revokePermissionTo($request->get('permisos', []));

        return $this->sendResponse(null, 'Permisos quitados con éxito.');

    }

    public function obtenerPermisosAsignados(Rol $rol)
    {
        $permisos = $rol->permissions;

        return $this->sendResponse($permisos ?? [], 'Permisos asignados recuperados con éxito.');
    }

    public function obtenerTodos()
    {
        $roles = Rol::all();

        return $this->sendResponse($roles->toArray(), 'roles recuperados con éxito.');
    }

}
