<?php

namespace App\Http\Controllers\Api\admin\Configuraciones;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Api\admin\Configuraciones\CreateConfiguracionApiRequest;
use App\Http\Requests\Api\admin\Configuraciones\UpdateConfiguracionApiRequest;
use App\Models\Configuracion;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class ConfiguracionApiController
 */
class ConfiguracionApiController extends AppbaseController implements HasMiddleware
{

    /**
     * //     * @return array
     * //     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Configuraciones', only: ['index']),
            new Middleware('permission:Ver Configuraciones', only: ['show']),
            new Middleware('permission:Crear Configuraciones', only: ['store']),
            new Middleware('permission:Editar Configuraciones', only: ['update']),
            new Middleware('permission:Eliminar Configuraciones', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Configuraciones.
     * GET|HEAD /configuraciones
     */
    public function index(Request $request): JsonResponse
    {
        $configuraciones = QueryBuilder::for(Configuracion::class)
            ->with([])
            ->allowedFilters([
                'key',
                'value',
                'descripcion'
            ])
            ->allowedSorts([
                'key',
                'value',
                'descripcion'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->jsonPaginate(request('page.size', 10));

        return $this->sendResponse($configuraciones->toArray(), 'configuraciones recuperados con éxito.');
    }


    /**
     * Store a newly created Configuracion in storage.
     * POST /configuraciones
     */
    public function store(CreateConfiguracionApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $configuraciones = Configuracion::create($input);

        return $this->sendResponse($configuraciones->toArray(), 'Configuracion creado con éxito.');
    }


    /**
     * Display the specified Configuracion.
     * GET|HEAD /configuraciones/{id}
     */
    public function show(Configuracion $configuracion)
    {
        return $this->sendResponse($configuracion->toArray(), 'Configuracion recuperado con éxito.');
    }


    /**
     * Update the specified Configuracion in storage.
     * PUT/PATCH /configuraciones/{id}
     */
    public function update(UpdateConfiguracionApiRequest $request, $id): JsonResponse
    {
        $configuracion = Configuracion::findOrFail($id);
        $configuracion->update($request->validated());
        return $this->sendResponse($configuracion, 'Configuracion actualizado con éxito.');
    }

    /**
     * Remove the specified Configuracion from storage.
     * DELETE /configuraciones/{id}
     */
    public function destroy(Configuracion $configuracion): JsonResponse
    {
        $configuracion->delete();
        return $this->sendResponse(null, 'Configuracion eliminado con éxito.');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */

    public function guardarGenerales(Request $request)
    {
        // Iniciamos la transacción
        DB::beginTransaction();

        try {
            // Actualización de textos
            Configuracion::find(Configuracion::NOMBRE_APLICACION)
                ->update(['value' => $request->input('nombre_aplicacion')]);

            Configuracion::find(Configuracion::EMAIL_APLICACION)
                ->update(['value' => $request->input('email_aplicacion')]);

            Configuracion::find(Configuracion::TELEFONO_APLICACION)
                ->update(['value' => $request->input('telefono_aplicacion')]);

            Configuracion::find(Configuracion::ESLOGAN)
                ->update(['value' => $request->input('eslogan_aplicacion')]);

            // Manejo de archivos (MediaLibrary)
            if ($request->hasFile('fondo_login_tema_oscuro')) {
                Configuracion::find(Configuracion::FONDO_LOGIN_TEMA_OSCURO)
                    ->addMedia($request->file('fondo_login_tema_oscuro'))
                    ->preservingOriginal()
                    ->toMediaCollection('fondo_login_tema_oscuro');
            }

            if ($request->hasFile('fondo_login_tema_claro')) {
                Configuracion::find(Configuracion::FONDO_LOGIN_TEMA_CLARO)
                    ->addMedia($request->file('fondo_login_tema_claro'))
                    ->preservingOriginal()
                    ->toMediaCollection('fondo_login_tema_claro');
            }

            if ($request->hasFile('logo')) {
                Configuracion::find(Configuracion::LOGO)
                    ->addMedia($request->file('logo'))
                    ->preservingOriginal()
                    ->toMediaCollection('logo');
            }

            // Si todo salió bien, confirmamos los cambios
            DB::commit();

            $configuracion = new Configuracion();
            return $this->sendResponse(
                $configuracion->getConfiguracionesGenerales(),
                'Configuraciones generales guardadas con éxito.'
            );

        } catch (Exception $e) {
            // Si algo falla, revertimos todo lo que se alcanzó a hacer en la DB
            DB::rollBack();

            // Es buena práctica loguear el error para debug
            \Log::error("Error al guardar configuraciones: " . $e->getMessage());

            return $this->sendError('Error al guardar los cambios.', $e->getMessage(), 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getConfiguracionesGenerales()
    {
        $configuraciones = new Configuracion();

        $generales = $configuraciones->getConfiguracionesGenerales();

        return $this->sendResponse($generales, 'Configuraciones generales recuperadas con éxito.');

    }

}
