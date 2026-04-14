<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\UpdateAsistenciaSesionTomaApiRequest;
use App\Models\AsistenciaSesionToma;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class AsistenciaSesionTomaApiController
 */
class AsistenciaSesionTomaApiController extends AppbaseController implements HasMiddleware
{

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Listar Asistencia Sesion Tomas', only: ['index']),
            new Middleware('permission:Ver Asistencia Sesion Tomas', only: ['show']),
            new Middleware('permission:Crear Asistencia Sesion Tomas', only: ['store']),
            new Middleware('permission:Editar Asistencia Sesion Tomas', only: ['update']),
            new Middleware('permission:Eliminar Asistencia Sesion Tomas', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Asistencia_sesion_tomas.
     * GET|HEAD /asistencia_sesion_tomas
     */
    public function index(Request $request): JsonResponse
    {
        $asistencia_sesion_tomas = QueryBuilder::for(AsistenciaSesionToma::class)
            ->allowedFilters([
    'hora_apertura',
    'hora_cierre',
    'codito_otp',
    'numero_toma',
    'sesion_id',
    'longitud_origen',
    'latitud_origen'
])
            ->allowedSorts([
    'hora_apertura',
    'hora_cierre',
    'codito_otp',
    'numero_toma',
    'sesion_id',
    'longitud_origen',
    'latitud_origen'
])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($asistencia_sesion_tomas, 'asistencia_sesion_tomas recuperados con éxito.');
    }


    /**

     * Store a newly created AsistenciaSesionToma in storage.
     * POST /asistencia_sesion_tomas
     */


// ...

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $input = $request->all();

            $input['hora_apertura'] = now();
            $input['hora_cierre'] = now()->addMinutes($request->input('minutos_tolerancia', 30)); // Agrega minutos de tolerancia a la hora de cierre



            $asistencia_sesion_tomas = AsistenciaSesionToma::create($input);

            DB::commit();

            return $this->sendResponse(
                $asistencia_sesion_tomas->toArray(),
                'Toma de asistencia aperturada con éxito.'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear Toma de Asistencia: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified AsistenciaSesionToma.
     * GET|HEAD /asistencia_sesion_tomas/{id}
     */
    public function show(AsistenciaSesionToma $asistenciasesiontoma)
    {
        return $this->sendResponse($asistenciasesiontoma->toArray(), 'AsistenciaSesionToma recuperado con éxito.');
    }

    /**
    * Update the specified AsistenciaSesionToma in storage.
    * PUT/PATCH /asistencia_sesion_tomas/{id}
    */
    public function update(UpdateAsistenciaSesionTomaApiRequest $request, $id): JsonResponse
    {
        $asistenciasesiontoma = AsistenciaSesionToma::findOrFail($id);
        $asistenciasesiontoma->update($request->validated());
        return $this->sendResponse($asistenciasesiontoma, 'AsistenciaSesionToma actualizado con éxito.');
    }

    /**
    * Remove the specified AsistenciaSesionToma from storage.
    * DELETE /asistencia_sesion_tomas/{id}
    */
    public function destroy(AsistenciaSesionToma $asistenciasesiontoma): JsonResponse
    {
        $asistenciasesiontoma->delete();
        return $this->sendResponse(null, 'AsistenciaSesionToma eliminado con éxito.');
    }
}
