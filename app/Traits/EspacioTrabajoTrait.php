<?php

namespace App\Traits;

use App\Models\EspacioTrabajo\TrabajoEspacio;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

trait EspacioTrabajoTrait
{
    /**
     * Procesa la entrada manual y devuelve los IDs de los usuarios.
     */
    private function extraerManual(array $carnets): array
    {
        if (empty($carnets)) {
            return [];
        }

        return User::whereIn('carnet', $carnets)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Guarda los Excels, los lee y extrae los IDs.
     */
    private function extraerDeExcel(TrabajoEspacio $espacio, array $archivos): array
    {
        $identificadoresEncontrados = [];

        foreach ($archivos as $archivo) {
            if ($archivo instanceof \Illuminate\Http\UploadedFile) {
                $espacio->addMedia($archivo)->toMediaCollection('importaciones_excel');

                $filas = Excel::toCollection(null, $archivo)->first();

                if ($filas && $filas->isNotEmpty()) {
                    $encabezados = $filas->first()->keys()->toArray();

                    $columnaCarnet = in_array('carnet', $encabezados) ? 'carnet' : null;
                    $columnaCorreo = collect($encabezados)->first(fn($k) => in_array($k, ['gmail', 'correo', 'email']));

                    // 4. Extraemos los datos
                    $datosFila = $filas->map(function ($fila) use ($columnaCarnet, $columnaCorreo) {
                        // Prioridad 1: Carnet
                        if ($columnaCarnet && !empty($fila[$columnaCarnet])) {
                            return ['tipo' => 'carnet', 'valor' => $fila[$columnaCarnet]];
                        }
                        // Prioridad 2: Correo
                        if ($columnaCorreo && !empty($fila[$columnaCorreo])) {
                            return ['tipo' => 'email', 'valor' => $fila[$columnaCorreo]];
                        }
                        return null;
                    })->filter()->values();

                    $identificadoresEncontrados = array_merge($identificadoresEncontrados, $datosFila->toArray());
                }
            }
        }

        if (empty($identificadoresEncontrados)) return [];

        // 5. Consulta eficiente a la BD para obtener los IDs
        $carnets = collect($identificadoresEncontrados)->where('tipo', 'carnet')->pluck('valor')->unique()->toArray();
        $emails = collect($identificadoresEncontrados)->where('tipo', 'email')->pluck('valor')->unique()->toArray();

        return User::whereIn('carnet', $carnets)
            ->orWhereIn('email', $emails)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Guarda los PDFs, extrae el texto usando OCR o Parser, y busca los carnets/correos.
     */
    private function extraerDePdf(TrabajoEspacio $espacio, array $archivos): array
    {
        $carnetsExtraidos = [];

        foreach ($archivos as $archivo) {
            if ($archivo instanceof \Illuminate\Http\UploadedFile) {
                // 1. Guardar como respaldo
                $espacio->addMedia($archivo)->toMediaCollection('importaciones_pdf');

                // 2. Aquí usas tu librería PDF (ej: spatie/pdf-to-text)
                // $texto = (new Pdf())->setPdf($archivo->path())->text();

                // 3. Usas Regex para atrapar carnets o correos del texto extraído
                // preg_match_all('/\b\d{4}-\d{5}\b/', $texto, $coincidencias);
                // $carnetsExtraidos = array_merge($carnetsExtraidos, $coincidencias[0]);
            }
        }

        return empty($carnetsExtraidos) ? [] : User::whereIn('carnet', $carnetsExtraidos)->pluck('id')->toArray();
    }

    /**
     * Guarda la imagen, la manda a IA (ej. AWS Rekognition) y devuelve los IDs.
     */
    private function extraerConIA(TrabajoEspacio $espacio, array $archivos): array
    {
        $carnetsExtraidos = [];

        foreach ($archivos as $archivo) {
            if ($archivo instanceof \Illuminate\Http\UploadedFile) {
                // 1. Guardar foto como respaldo (importante para auditorías)
                $espacio->addMedia($archivo)->toMediaCollection('importaciones_ia');

                // 2. Aquí llamas a tu servicio de IA (ej: AWS Rekognition detectText)
                // $resultadosIA = AwsVision::detectText($archivo->path());

                // 3. Filtras y buscas los carnets de los resultados de la IA
                // ...
            }
        }

        return empty($carnetsExtraidos) ? [] : User::whereIn('carnet', $carnetsExtraidos)->pluck('id')->toArray();
    }

}
