<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\AsistenciaConfiguracion;
use App\Models\EspacioTrabajo\TrabajoEspacio;
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
    public function store(Request $request): JsonResponse
    {

        $request->validate([
            'espacio_id' => 'required',
            'requiere_ubicacion' => 'required',
            'requiere_prueba_vida' => 'required',
            'cantidad_tomas' => 'required',
            'requiere_codigo_otp' => 'required',
            'minutos_tolerancia' => 'required',
        ]);

        try {
            $configuration = AsistenciaConfiguracion::create([
                'requiere_ubicacion' => $request->input('requiere_ubicacion'),
                'requiere_prueba_vida' => $request->input('requiere_prueba_vida'),
                'requiere_codigo_otp' => $request->input('requiere_codigo_otp'),
                'cantidad_tomas_requeridas' => $request->input('cantidad_tomas'),
                'minutos_tolerancia' => $request->input('minutos_tolerancia'),
                'espacio_id' => $request->input('espacio_id'),
            ]);

            AsistenciaSesion::create([
                'fecha' => now(),
                'estado' => AsistenciaSesion::EN_CURSO,
                'espacio_id' => $request->input('espacio_id'),
                'configuracion_id' => $configuration->id
            ]);

            $espacio = TrabajoEspacio::findOrFail($request->input('espacio_id'));

            $espacio->update([
                'estado' => TrabajoEspacio::ACTIVO
            ]);
        } catch (\Exception $e) {
            return $this->sendError('Error al crear Sesión: ' . $e->getMessage(), 500);
        }

        return $this->sendSuccess('Sesión creada con éxito.');
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
