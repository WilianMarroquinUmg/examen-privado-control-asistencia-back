<?php

namespace App\Http\Controllers\Api\EspacioTrabajo;

use App\Http\Controllers\AppBaseController;
use App\Models\AsistenciaSesion;
use App\Models\User;
use App\Traits\EspacioTrabajoTrait;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Api\EspacioTrabajo\CreateTrabajoEspacioApiRequest;
use App\Http\Requests\Api\EspacioTrabajo\UpdateTrabajoEspacioApiRequest;
use App\Models\EspacioTrabajo\TrabajoEspacio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Class TrabajoEspacioApiController
 */
class TrabajoEspacioApiController extends AppbaseController implements HasMiddleware
{

    use EspacioTrabajoTrait;

    /**
     * @return array
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:Ver Trabajo Espacios', only: ['index']),
            new Middleware('permission:Ver Trabajo Espacios', only: ['show']),
            new Middleware('permission:Crear Trabajo Espacios', only: ['store']),
            new Middleware('permission:Editar Trabajo Espacios', only: ['update']),
            new Middleware('permission:Eliminar Trabajo Espacios', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the Trabajo_espacios.
     * GET|HEAD /trabajo_espacios
     */
    public function index(Request $request): JsonResponse
    {
        $trabajo_espacios = QueryBuilder::for(TrabajoEspacio::class)
            ->allowedFilters([
                'catedratico_id',
                'facultad_id',
                'ciclo_id',
                'curso_id',
                AllowedFilter::scope('buscar', 'buscar')
            ])
            ->allowedSorts([
                'catedratico_id',
                'facultad_id',
                'ciclo_id',
                'curso_id'
            ])
            ->allowedIncludes([
                'ciclo',
                'curso',
                'facultad',
                'catedratico'
            ])
            ->defaultSort('-id') // Ordenar por defecto por fecha descendente
            ->Paginate(request('page.size') ?? 10);

        return $this->sendResponse($trabajo_espacios, 'trabajo_espacios recuperados con éxito.');
    }


    /**
     * Store a newly created TrabajoEspacio in storage.
     * POST /trabajo_espacios
     */

    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $espacio = TrabajoEspacio::create([
                'facultad_id' => $request->input('facultad_id'),
                'ciclo_id' => $request->input('ciclo_id'),
                'curso_id' => $request->input('curso_id'),
                'catedratico_id' => Auth::id(),
            ]);

            $datosInscripcion = $request->all()['alumnos'] ?? [];
            $todosLosAlumnosIds = [];

            foreach ($datosInscripcion as $item) {
                $tipo = $item['tipoInscripcion'] ?? null;

                switch ($tipo) {
                    case 'manual':
                        $idsExtraidos = $this->extraerManual($item['alumnos'] ?? []);
                        $todosLosAlumnosIds = array_merge($todosLosAlumnosIds, $idsExtraidos);
                        break;

                    case 'excel':
                        $idsExtraidos = $this->extraerDeExcel($espacio, $item['documento'] ?? []);
                        $todosLosAlumnosIds = array_merge($todosLosAlumnosIds, $idsExtraidos);
                        break;

                    case 'pdf':
                        $idsExtraidos = $this->extraerDePdf($espacio, $item['documento'] ?? []);
                        $todosLosAlumnosIds = array_merge($todosLosAlumnosIds, $idsExtraidos);
                        break;

                    case 'ia':
                        $idsExtraidos = $this->extraerConIA($espacio, $item['documento'] ?? []);
                        $todosLosAlumnosIds = array_merge($todosLosAlumnosIds, $idsExtraidos);
                        break;
                }
            }

            $todosLosAlumnosIds = array_unique($todosLosAlumnosIds);

            if (!empty($todosLosAlumnosIds)) {
                $espacio->alumnos()->syncWithoutDetaching($todosLosAlumnosIds);
            }

            DB::commit();

            return $this->sendSuccess('Inscripción procesada con éxito.');

        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Error al procesar la inscripción: '.$e->getMessage());
        }
    }

    /**
     * Display the specified TrabajoEspacio.
     * GET|HEAD /trabajo_espacios/{id}
     */
    public function show(TrabajoEspacio $trabajo_espacio)
    {
        $trabajo_espacio->load([
            'facultad',
            'ciclo',
            'curso',
            'sesiones',
        ]);
        return $this->sendResponse($trabajo_espacio->toArray(), 'TrabajoEspacio recuperado con éxito.');
    }

    /**
     * Update the specified TrabajoEspacio in storage.
     * PUT/PATCH /trabajo_espacios/{id}
     */
    public function update(UpdateTrabajoEspacioApiRequest $request, $id): JsonResponse
    {
        $trabajoespacio = TrabajoEspacio::findOrFail($id);
        $trabajoespacio->update($request->validated());
        return $this->sendResponse($trabajoespacio, 'TrabajoEspacio actualizado con éxito.');
    }

    /**
     * Remove the specified TrabajoEspacio from storage.
     * DELETE /trabajo_espacios/{id}
     */
    public function destroy(TrabajoEspacio $trabajo_espacio): JsonResponse
    {
        $trabajo_espacio->delete();
        return $this->sendResponse(null, 'TrabajoEspacio eliminado con éxito.');
    }

    public function getListadoAsistencia($espacioId)
    {
//        $request->validate([
//            'espacio_id' => 'required|exists:trabajo_espacios,id',
//        ]);

        try {
            // 1. Traer las sesiones válidas con sus relaciones (Puro Eloquent)
            $sesiones = AsistenciaSesion::with(['configuration', 'tomas'])
                ->where('espacio_id', $espacioId)
                ->where('fecha', '<=', now())
                ->get();

            $totalSesiones = $sesiones->count();

            // Extraemos un arreglo plano (array de IDs) con todas las tomas de estas sesiones
            $tomasValidasIds = $sesiones->pluck('tomas')->flatten()->pluck('id');

            // 2. Traer el espacio con los alumnos y precargar (Eager Load) solo sus registros útiles
            $espacio = TrabajoEspacio::with(['alumnos' => function ($query) use ($tomasValidasIds) {
                $query->with(['asistenciaRegistros' => function ($rq) use ($tomasValidasIds) {
                    // Filtramos para traer solo registros aprobados que pertenezcan a las tomas del curso
                    $rq->whereIn('toma_asistencia_id', $tomasValidasIds)
                        ->where('fue_aprobada', 1);
                }]);
            }])->findOrFail($espacioId);

            // 3. Procesamiento Matemático con Collections (La Magia de Laravel)
            $dataReporte = $espacio->alumnos->map(function (User $alumno) use ($sesiones, $totalSesiones) {
                $sesionesCompletadas = 0;

                // Extraemos solo los IDs de las tomas a las que asistió este alumno
                $tomasAsistidasIds = $alumno->asistenciaRegistros->pluck('toma_asistencia_id');

                // Evaluamos sesión por sesión
                foreach ($sesiones as $sesion) {
                    $tomasRequeridas = (int) $sesion->configuration->cantidad_tomas_requeridas;
                    $tomasDeEstaSesionIds = $sesion->tomas->pluck('id');

                    // 🔥 EL TRUCO SENIOR: Intersect
                    // Comparamos las tomas de la sesión con las tomas que el alumno registró.
                    // Si la sesión tiene las tomas [1, 2, 3] y el alumno fue a [2, 3, 4], intersect devuelve [2, 3].
                    $coincidencias = $tomasDeEstaSesionIds->intersect($tomasAsistidasIds)->count();

                    // Validamos la regla estricta
                    if ($coincidencias >= $tomasRequeridas) {
                        $sesionesCompletadas++;
                    }
                }

                // Calculamos porcentaje
                $porcentaje = $totalSesiones > 0 ? round(($sesionesCompletadas / $totalSesiones) * 100, 2) : 0;

                return [
                    'id' => $alumno->id,
                    'nombre' => $alumno->nombre_completo,
                    'carnet' => $alumno->carnet ?? 'Sin Carnet',
                    'asistencias_completas' => $sesionesCompletadas,
                    'total_sesiones' => $totalSesiones,
                    'porcentaje' => $porcentaje,
                    'estado' => $porcentaje >= 75 ? 'Cumple' : 'En Riesgo Crítico'
                ];
            });

            return $this->sendResponse($dataReporte, 'Reporte generado con éxito.');

        } catch (\Exception $e) {
            Log::error("Error en reporte estricto Eloquent: " . $e->getMessage());
            return $this->sendError('Error al generar el reporte estricto.', 500);
        }

    }


}
