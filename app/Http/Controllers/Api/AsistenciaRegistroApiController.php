<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\AsistenciaSesionToma;
use Aws\Rekognition\RekognitionClient;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\CreateAsistenciaRegistroApiRequest;
use App\Http\Requests\Api\UpdateAsistenciaRegistroApiRequest;
use App\Models\AsistenciaRegistro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            new Middleware('permission:Ver Asistencia Registros', only: ['index']),
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
     * Registra la asistencia validando OTP, GPS (Haversine) y Liveness de AWS.
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $toma = AsistenciaSesionToma::with('sesion.configuration')->findOrFail($request->toma_id);
            $config = $toma->sesion->configuration;

            // 1. Evitar duplicados
            if (AsistenciaRegistro::where('toma_asistencia_id', $toma->id)->where('alumno_id', $user->id)->exists()) {
                return $this->sendError('Ya has registrado tu asistencia en esta toma.', 400);
            }

            // 2. Validación de PIN (OTP)
            if ($config->requiere_codigo_otp && ($request->codigo_otp !== $toma->codigo_otp)) {
                return $this->sendError('El código OTP es incorrecto.', 400);
            }

            // 3. Validación de Coordenadas (Haversine)
            if ($config->requiere_ubicacion) {
                if (!$request->filled(['latitud', 'longitud'])) {
                    return $this->sendError('Coordenadas GPS requeridas.', 400);
                }

                $distanciaCalculada = $this->calcularDistancia(
                    (float) $request->latitud, (float) $request->longitud,
                    (float) $toma->latitud_origen, (float) $toma->longitud_origen
                );

                if ($distanciaCalculada > $toma->radio_metros) {
                    return $this->sendError("Fuera de rango. Estás a " . round($distanciaCalculada) . "m del punto de origen.", 403);
                }
            }

            $livenessScore = null;
            $awsResult = null;
            $similitudFacial = null; // Guardaremos esto si queremos auditoría

            // 4. VALIDACIÓN BIOMÉTRICA DUAL (Liveness + CompareFaces)
            if ($config->requiere_prueba_vida) {

                // 4.0 Seguridad Preventiva: ¿Tiene foto certificada?
                if (!$user->tiene_foto_certificada) {
                    return $this->sendError('No tienes un perfil biométrico certificado. No puedes registrar asistencia por IA.', 403);
                }

                $rekognition = new \Aws\Rekognition\RekognitionClient([
                    'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
                    'version' => 'latest',
                    'credentials' => [
                        'key'    => env('AWS_ACCESS_KEY_ID'),
                        'secret' => env('AWS_SECRET_ACCESS_KEY'),
                    ]
                ]);

                // 4.1 FASE 1: ¿Es un humano vivo? (Liveness)
                $awsResult = $rekognition->getFaceLivenessSessionResults(['SessionId' => $request->aws_session_id]);
                $confidence = $awsResult->get('Confidence');

                if ($awsResult->get('Status') !== 'SUCCEEDED' || $confidence < 90.0) {
                    return $this->sendError('Fallo en la prueba biométrica de vida. Grado de confianza: '. round($confidence, 2) . '%', 401);
                }
                $livenessScore = $confidence;

                // 4.2 FASE 2: ¿Es el alumno correcto? (CompareFaces)
                // Extraemos los bytes de la foto maestra desde Spatie usando Streams (funciona en local y en S3)
                $fotoMaestra = $user->fotoCertificada;
                $stream = $fotoMaestra->stream();
                $bytesFotoMaestra = stream_get_contents($stream);
                fclose($stream);

                // Extraemos los bytes de la foto en vivo que capturó el Liveness
                $bytesFotoEnVivo = $awsResult->get('ReferenceImage')['Bytes'];

                // Comparamos
                $compareResult = $rekognition->compareFaces([
                    'SourceImage' => [
                        'Bytes' => $bytesFotoMaestra,
                    ],
                    'TargetImage' => [
                        'Bytes' => $bytesFotoEnVivo,
                    ],
                    'SimilarityThreshold' => 85.0, // 85% es un buen margen para lentes/barba/cortes de pelo
                ]);

                // Si AWS devuelve un array de 'FaceMatches' vacío, significa que es OTRA persona
                if (empty($compareResult->get('FaceMatches'))) {
                    return $this->sendError('Alerta de Fraude: El rostro validado no coincide con el perfil certificado de este alumno.', 403);
                }

                // Si hace match, guardamos el porcentaje (opcional, por si lo agregas a la BD)
                $similitudFacial = $compareResult->get('FaceMatches')[0]['Similarity'];
            }

            // 5. Creación del Registro
            $registro = AsistenciaRegistro::create([
                'hora_registro'      => Carbon::now()->format('H:i:s'),
                'latitud'            => $request->latitud,
                'longitud'           => $request->longitud,
                'aws_liveness_score' => $livenessScore,
                'fue_aprobada'       => 1,
                'toma_asistencia_id' => $toma->id,
                'alumno_id'          => $user->id,
                // 'similitud_facial'   => $similitudFacial, // Descomenta si agregas esta columna a tu DB para auditorías
            ]);

            // 6. Almacenar Imagen de Evidencia (Spatie Media Library)
            if ($awsResult && $awsResult->hasKey('ReferenceImage')) {
                $user->addMediaFromBase64(base64_encode($bytesFotoEnVivo))
                    ->usingFileName("evidencia_{$user->id}_{$toma->id}_" . time() . ".jpg")
                    ->toMediaCollection('asistencia_evidencias');
            }

            DB::commit();
            return $this->sendResponse($registro->toArray(), '¡Asistencia biométrica verificada y aprobada!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en registro de asistencia (User ID: " . Auth::id() . "): " . $e->getMessage());
            return $this->sendError('Error interno al procesar el registro.', 500);
        }
    }

    /**
     * Calcula la distancia entre dos puntos usando la fórmula de Haversine.
     */
    private function calcularDistancia($lat1, $lon1, $lat2, $lon2): float
    {
        $radioTierra = 6371000; // Radio en metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $radioTierra * $c;
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
