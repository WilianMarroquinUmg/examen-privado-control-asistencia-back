<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAsistenciaRegistroApiRequest;
use App\Http\Requests\Api\UpdateAsistenciaRegistroApiRequest;
use App\Models\AsistenciaRegistro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class AsistenciaRegistroApiController
 */
class AsistenciaRegistroApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Asistencia Registros', only: ['index']),
            new Middleware('permission:Ver Asistencia Registros', only: ['show']),
            new Middleware('permission:Crear Asistencia Registros', only: ['store']),
            new Middleware('permission:Editar Asistencia Registros', only: ['update']),
            new Middleware('permission:Eliminar Asistencia Registros', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Asistencia_registros.
     * GET|HEAD /asistencia_registros
     */
    public function index(Request $request): JsonResponse
    {
        $asistencia_registros = QueryBuilder::for(AsistenciaRegistro::class)
            ->allowedFilters([
                'hora_registro',
                'latitud',
                'longitud',
                'foto_evidencia_url',
                'aws_liveness_score',
                'fue_aprobada',
                'toma_asistencia_id',
                'alumno_id'
            ])
            ->allowedSorts([
                'hora_registro',
                'latitud',
                'longitud',
                'foto_evidencia_url',
                'aws_liveness_score',
                'fue_aprobada',
                'toma_asistencia_id',
                'alumno_id'.
                'created_at'
            ])
            ->AllowedIncludes([
                'alumno'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($asistencia_registros, 'asistencia_registros recuperados con éxito.');
    }


    /**
     * Store a newly created AsistenciaRegistro in storage.
     * POST /asistencia_registros
     */
    public function store(CreateAsistenciaRegistroApiRequest $request): JsonResponse
    {
        $input = $request->all();

        $asistencia_registros = AsistenciaRegistro::create($input);

        return $this->sendResponse($asistencia_registros->toArray(), 'AsistenciaRegistro creado con éxito.');
    }

    /**
     * Display the specified AsistenciaRegistro.
     * GET|HEAD /asistencia_registros/{id}
     */
    public function show(AsistenciaRegistro $asistenciaregistro)
    {
        return $this->sendResponse($asistenciaregistro->toArray(), 'AsistenciaRegistro recuperado con éxito.');
    }

    /**
     * Update the specified AsistenciaRegistro in storage.
     * PUT/PATCH /asistencia_registros/{id}
     */
    public function update(UpdateAsistenciaRegistroApiRequest $request, $id): JsonResponse
    {
        $asistenciaregistro = AsistenciaRegistro::findOrFail($id);
        $asistenciaregistro->update($request->validated());
        return $this->sendResponse($asistenciaregistro, 'AsistenciaRegistro actualizado con éxito.');
    }

    /**
     * Remove the specified AsistenciaRegistro from storage.
     * DELETE /asistencia_registros/{id}
     */
    public function destroy(AsistenciaRegistro $asistenciaregistro): JsonResponse
    {
        $asistenciaregistro->delete();
        return $this->sendResponse(null, 'AsistenciaRegistro eliminado con éxito.');
    }
}
