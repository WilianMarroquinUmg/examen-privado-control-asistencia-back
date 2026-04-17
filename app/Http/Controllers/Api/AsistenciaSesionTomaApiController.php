<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Traits\AsistenciaRegistroTrait;
use Aws\Exception\AwsException;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\UpdateAsistenciaSesionTomaApiRequest;
use App\Models\AsistenciaSesionToma;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class AsistenciaSesionTomaApiController
 */
class AsistenciaSesionTomaApiController extends AppbaseController implements HasMiddleware
{

    use AsistenciaRegistroTrait;
    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Asistencia Sesion Tomas', only: ['index']),
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
                'latitud_origen',
                AllowedFilter::scope('soloActivasParaAlumno', 'soloActivasParaAlumno'),
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
            ->allowedIncludes([
                'sesion.espacio.curso',
                'sesion.configuration'
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
            $input['hora_cierre'] = now()->addMinutes($request->input('minutos_tolerancia',
                30)); // Agrega minutos de tolerancia a la hora de cierre


            $asistencia_sesion_tomas = AsistenciaSesionToma::create($input);

            DB::commit();

            return $this->sendResponse(
                $asistencia_sesion_tomas->toArray(),
                'Toma de asistencia aperturada con éxito.'
            );

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al crear Toma de Asistencia: '.$e->getMessage(), [
                'request' => $request->all()
            ]);

            return $this->sendError($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified AsistenciaSesionToma.
     * GET|HEAD /asistencia_sesion_tomas/{id}
     */
    public function show(AsistenciaSesionToma $asistencia_sesion_toma)
    {
        $asistencia_sesion_toma->load([
           'sesion.espacio.curso',
           'sesion.configuration',
        ]);
        return $this->sendResponse($asistencia_sesion_toma->toArray(), 'AsistenciaSesionToma recuperado con éxito.');
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

    public function solicitarLiveness(Request $request): JsonResponse
    {
        $request->validate([
            'toma_id' => 'required|exists:asistencia_sesion_tomas,id'
        ]);

        $toma = AsistenciaSesionToma::select('id', 'hora_cierre')
            ->find($request->input('toma_id'));

        if($this->tomaEstaVencida($toma)){
            return $this->sendError('La toma de asistencia ha vencido. No se puede iniciar el proceso de liveness.', 407);
        }

        try {
            // 🚀 1. Opciones Base (Obligatorias en ambos entornos)
            $awsOptions = [
                'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'version' => 'latest'
            ];

            // 🚀 2. Validación Maestra de Entorno
            // Si estamos en local, inyectamos las llaves del .env
            // Si estamos en producción (Serverless), lo omitimos para que use el IAM Role
            if (app()->environment('local')) {
                $awsOptions['credentials'] = [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ];
            }

            // 3. Instanciamos el cliente con las opciones dinámicas
            $rekognition = new RekognitionClient($awsOptions);

            $result = $rekognition->createFaceLivenessSession([]);
            $sessionId = $result->get('SessionId');

            return $this->sendResponse([
                'session_id' => $sessionId,
            ],
                'Liveness session creada exitosamente. Usa este ID para iniciar el escaneo facial.'
            );

        } catch (AwsException $e) {
            return $this->sendError('AWS Error: ' . $e->getAwsErrorMessage(), 500);
        } catch (\Exception $e) {
            return $this->sendError('Error de Laravel: ' . $e->getMessage(), 500);
        }
    }

}
