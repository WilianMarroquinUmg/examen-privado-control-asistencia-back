<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PerfilBiometricoApiController extends Controller
{
    /**
     * Obtiene el estado actual y la foto biométrica del alumno autenticado.
     */
    public function show(): JsonResponse
    {
        try {
            $user = Auth::user();

            // 1. Buscamos primero si tiene una pendiente. Si no, buscamos la certificada.
            // Gracias a las relaciones Eloquent que creamos, esto es súper limpio.
            $foto = $user->fotoPendienteCertificacion ?? $user->fotoCertificada;

            // Si no tiene ninguna de las dos, devolvemos un 404 controlado para el frontend
            if (!$foto) {
                return response()->json([
                    'existe' => false,
                    'message' => 'No hay perfil biométrico registrado.'
                ], 404);
            }

            // 2. MAGIA DE ARQUITECTO (URLs Temporales)
            // Si usamos S3, generamos un link que expira en 5 minutos para que nadie pueda robar la imagen.
            // Si estás en local (public), usamos getUrl normal.
            $discoConfigurado = config('filesystems.default'); // 's3' o 'public'

            $url = $discoConfigurado === 's3'
                ? $foto->getTemporaryUrl(now()->addMinutes(5))
                : $foto->getUrl();

            return response()->json([
                'existe' => true,
                'url' => $url,
                'fue_certificada' => (bool) $foto->fue_certificada
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener perfil biométrico: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener los datos.'], 500);
        }
    }

    /**
     * Recibe la foto del frontend, limpia las viejas y la sube a S3.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validación estricta (Solo imágenes y máximo 5MB)
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'foto.required' => 'Debes enviar una imagen.',
            'foto.image' => 'El archivo debe ser una imagen válida.',
            'foto.max' => 'La imagen no debe pesar más de 5MB.',
        ]);

        try {
            $user = Auth::user();

            // 2. HIGIENE EN LA NUBE (Súper importante)
            // Borramos cualquier foto anterior de esta colección.
            // Esto evita que si un alumno sube 10 fotos seguidas, AWS te cobre por 10 archivos huérfanos.
            $user->clearMediaCollection('foto_perfil_biometrico');

            // 3. Subida directa con Spatie
            $media = $user->addMediaFromRequest('foto')
                ->usingFileName("biometria_{$user->id}_" . time() . ".jpg")
                ->toMediaCollection('foto_perfil_biometrico'); // Spatie usará el disco default (s3) automáticamente

            // 4. Aseguramos el estado de la bandera en la base de datos
            // Como agregaste la columna directamente a la migración de Spatie, la actualizamos así:
            $media->fue_certificada = false;
            $media->save();

            return response()->json([
                'success' => true,
                'message' => 'Fotografía subida con éxito. Pendiente de revisión por el catedrático.',
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error al guardar foto biométrica: ' . $e->getMessage(), [
                'user_id' => Auth::id() ?? 'Desconocido'
            ]);
            return response()->json(['error' => 'No se pudo guardar la fotografía en el servidor.'], 500);
        }
    }

    public function certificar($id): JsonResponse
    {
        try {
            $alumno = User::findOrFail($id);
            $fotoPendiente = $alumno->fotoPendienteCertificacion;

            if (!$fotoPendiente) {
                return response()->json(['error' => 'No hay fotografía pendiente para certificar.'], 400);
            }

            // 🚀 HIGIENE DE NUBE (Nivel Senior)
            // Si el alumno ya tenía una foto certificada vieja, la borramos físicamente
            // para no acumular basura en Amazon S3.
            $fotoVieja = $alumno->fotoCertificada;
            if ($fotoVieja && $fotoVieja->id !== $fotoPendiente->id) {
                $fotoVieja->delete();
            }

            // Actualizamos la bandera a TRUE
            $fotoPendiente->fue_certificada = true;
            $fotoPendiente->save();

            return response()->json([
                'success' => true,
                'message' => 'Fotografía certificada exitosamente.'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al certificar foto del alumno {$id}: ".$e->getMessage());
            return response()->json(['error' => 'No se pudo certificar la fotografía.'], 500);
        }
    }
    public function showInfoAlumno($id): JsonResponse
    {
        try {
            $alumno = User::findOrFail($id);
            $foto = $alumno->fotoPendienteCertificacion;

            if (!$foto) {
                return response()->json(['error' => 'El alumno no tiene fotos pendientes de certificación.'], 404);
            }

            // Generamos la URL segura que se autodestruye
            $disco = config('filesystems.default');
            $url = $disco === 's3'
                ? $foto->getTemporaryUrl(now()->addMinutes(5))
                : $foto->getUrl();

            return response()->json([
                'success' => true,
                'url' => $url
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al obtener foto pendiente del alumno {$id}: ".$e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al cargar la fotografía.'], 500);
        }
    }
    public function rechazar($id): JsonResponse
    {
        try {
            $alumno = User::findOrFail($id);
            $fotoPendiente = $alumno->fotoPendienteCertificacion;

            if (!$fotoPendiente) {
                return response()->json(['error' => 'No hay fotografía pendiente para rechazar.'], 400);
            }

            // Al rechazarla, la borramos físicamente del disco/S3
            $fotoPendiente->delete();

            // Aquí podrías disparar un evento o notificación (ej. $alumno->notify(...))
            // para avisarle por correo que su foto fue rechazada y debe subir otra.

            return response()->json([
                'success' => true,
                'message' => 'Fotografía rechazada y eliminada.'
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error al rechazar foto del alumno {$id}: ".$e->getMessage());
            return response()->json(['error' => 'No se pudo rechazar la fotografía.'], 500);
        }
    }
}
