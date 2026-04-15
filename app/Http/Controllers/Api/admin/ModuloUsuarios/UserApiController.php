<?php

namespace App\Http\Controllers\Api\admin\ModuloUsuarios;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\admin\ModuloUsuarios\CreateUserApiRequest;
use App\Http\Requests\Api\admin\ModuloUsuarios\UpdateUserApiRequest;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class UserApiController
 */
class UserApiController extends AppbaseController implements HasMiddleware
{

    /**
     * //     * @return array
     * //     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Usuarios', only: ['index']),
            new Middleware('permission:Ver Usuarios', only: ['show', 'obtenerRolesDeUser']),
            new Middleware('permission:Crear Usuarios', only: ['store']),
            new Middleware('permission:Editar Usuarios', only: ['update', 'asignarRolAUser', 'quitarRolAUser']),
            new Middleware('permission:Eliminar Usuarios', only: ['destroy']),
            new Middleware('permission:Actualizar Perfil Usuario', only: ['actualizarFotoPerfil']),
            new Middleware('permission:Ver Perfil Usuario', only: ['getDataPerfil']),
        ];
    }

    /**
     * Display a listing of the Users.
     * GET|HEAD /users
     */
    public function index(Request $request): JsonResponse
    {
        $users = QueryBuilder::for(User::class)
            ->allowedIncludes([
                'roles',
            ])
            ->allowedFilters([
                'primer_nombre',
                'segundo_nombre',
                'primer_apellido',
                'segundo_apellido',
                'usuario',
                'email',
                AllowedFilter::scope('busquedaAvanzada', 'busquedaAvanzada'),
                AllowedFilter::scope('sinUsuarioIds', 'sinUsuarioIds'),
            ])
            ->allowedSorts([
                'id',
                'primer_nombre',
                'segundo_nombre',
                'primer_apellido',
                'segundo_apellido',
                'usuario',
                'email',
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->paginate($request->get('per_page', 10));

        return $this->sendResponse($users->toArray(), 'users recuperados con éxito.');
    }


    /**
     * Store a newly created User in storage.
     * POST /users
     */
    public function store(CreateUserApiRequest $request): JsonResponse
    {
        $input = $request->all();

        if ($input['password'] != $input['password_confirmation']) {
            return $this->sendError('Las contraseñas no coinciden', 400);
        }

        $input['password'] = bcrypt($input['password']);

        $users = User::create($input);

        return $this->sendResponse($users->toArray(), 'User creado con éxito.');
    }


    /**
     * Display the specified User.
     * GET|HEAD /users/{id}
     */
    public function show(User $user)
    {
        $data = [
            'user' => $user,
            'roles' => $user->roles,
            'permisos' => $user->getAllPermissions(),
        ];

        return $this->sendResponse($data, 'User recuperado con éxito.');
    }


    /**
     * Update the specified User in storage.
     * PUT/PATCH /users/{id}
     */
    public function update(UpdateUserApiRequest $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->validated());
        return $this->sendResponse($user, 'User actualizado con éxito.');
    }

    /**
     * Remove the specified User from storage.
     * DELETE /users/{id}
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return $this->sendResponse(null, 'User eliminado con éxito.');
    }

    /**
     * Obtiene los roles asignados a un usuario.
     * @param  User  $user
     * @return JsonResponse
     */
    public function obtenerRolesDeUser(User $user)
    {
        $roles = $user->roles;

        if ($roles->isEmpty()) {
            return $this->sendResponse(null, 'El usuario no tiene roles asignados.');
        }

        return $this->sendResponse($roles->toArray(), 'Roles recuperados con éxito.');
    }

    /**
     * Asigna un rol a un usuario.
     * @param  Request  $request
     * @return JsonResponse
     */
    public function asignarRolAUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'rol_id' => ['required', 'exists:roles,id'],
        ]);

        $user = User::find($validated['user_id']);
        $rol = Rol::find($validated['rol_id']);

        if (!$user || !$rol) {
            return $this->sendError('Usuario o rol no encontrado.', 404);
        }

        $user->assignRole($rol);

        return $this->sendResponse(null, 'Rol asignado con éxito.');
    }

    /**
     * Quita un rol a un usuario.
     * @param  Request  $request
     * @return JsonResponse
     */
    public function quitarRolAUser(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'rol_id' => ['required', 'exists:roles,id'],
        ]);

        $user = User::find($validated['user_id']);
        $rol = Rol::find($validated['rol_id']);

        if (!$user || !$rol) {
            return $this->sendError('Usuario o rol no encontrado.', 404);
        }

        $user->removeRole($rol);

        return $this->sendResponse(null, 'Se ha quitado el rol con éxito.');
    }

    /**
     * Obtiene los datos del perfil de un usuario.
     * @param  User  $user
     * @return JsonResponse
     */
    public function getDataPerfil(User $user)
    {
        $user->load([
            'roles' => function ($query) {
                $query->select('id', 'name');
            },
            'estado' => function ($query) {
                $query->select('id', 'nombre');
            }
        ]);

        $data = [
            'datos_usuario' => array_merge(
                $user->toArray(),
                [
                    'avatar' => optional($user->getMedia('avatars')->last())->getUrl(),
                ]
            ),
        ];


        return $this->sendResponse($data, 'Datos del perfil del usuario recuperados con éxito.');

    }

    public function actualizarFotoPerfil(User $user, Request $request)
    {

        if (!$request->hasFile('avatar')) {
            return $this->sendError('No se ha enviado ninguna imagen.', 400);
        }

        $media = $user->addMedia($request->file('avatar'))
            ->preservingOriginal()
            ->toMediaCollection('avatars');

        if (!$media) {
            return $this->sendError('Error al subir la imagen.', 500);
        }

        return $this->sendResponse($media->getUrl(), 'Foto de perfil actualizada con éxito.');

    }
}
