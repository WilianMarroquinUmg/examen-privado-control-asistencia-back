<?php

namespace App\Traits;

use App\Models\EspacioTrabajo\TrabajoEspacio;
use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Smalot\PdfParser\Parser;
use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

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
     * Procesa archivos Excel dinámicamente, ignorando filas basura al inicio,
     * detectando columnas clave y limpiando los datos (ej. espacios en carnets).
     */

    private function extraerDeExcel(TrabajoEspacio $espacio, array $archivos): array
    {
        $identificadoresEncontrados = [];

        foreach ($archivos as $archivo) {
            if ($archivo instanceof \Illuminate\Http\UploadedFile) {

                // 🚀 1. PRIMERO leemos el Excel (mientras sigue en la carpeta temporal)
                $filas = Excel::toCollection(null, $archivo)->first();

                // 🚀 2. LUEGO guardamos el respaldo (Spatie mueve el archivo a su destino final)
                $espacio->addMedia($archivo)->toMediaCollection('importaciones_excel');

                // 3. Ya con los datos en memoria, hacemos la limpieza
                if ($filas && $filas->isNotEmpty()) {
                    $indiceCarnet = null;
                    $indiceCorreo = null;
                    $estamosEnDatos = false;

                    foreach ($filas as $fila) {
                        if (!$estamosEnDatos) {
                            $filaTexto = $fila->map(function($item) {
                                return strtolower(trim(Str::ascii($item)));
                            })->toArray();

                            foreach ($filaTexto as $index => $valor) {
                                if (str_contains((string) $valor, 'carne') || str_contains((string) $valor, 'carnet')) {
                                    $indiceCarnet = $index;
                                }
                                if (str_contains((string) $valor, 'correo') || str_contains((string) $valor, 'email')) {
                                    $indiceCorreo = $index;
                                }
                            }

                            if ($indiceCarnet !== null || $indiceCorreo !== null) {
                                $estamosEnDatos = true;
                                continue;
                            }
                        }
                        else {
                            if ($indiceCarnet !== null && !empty($fila[$indiceCarnet])) {
                                $carnetLimpio = preg_replace('/\s+/', '', $fila[$indiceCarnet]);
                                $identificadoresEncontrados[] = ['tipo' => 'carnet', 'valor' => $carnetLimpio];
                            }
                            elseif ($indiceCorreo !== null && !empty($fila[$indiceCorreo])) {
                                $identificadoresEncontrados[] = ['tipo' => 'email', 'valor' => trim($fila[$indiceCorreo])];
                            }
                        }
                    }
                }
            }
        }

        if (empty($identificadoresEncontrados)) return [];

        // 4. Consulta final
        $carnets = collect($identificadoresEncontrados)->where('tipo', 'carnet')->pluck('valor')->unique()->toArray();
        $emails  = collect($identificadoresEncontrados)->where('tipo', 'email')->pluck('valor')->unique()->toArray();

        return User::whereIn('carnet', $carnets)
            ->orWhereIn('email', $emails)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Guarda los PDFs, extrae el texto y busca carnets/correos usando Regex.
     */
    private function extraerDePdf(TrabajoEspacio $espacio, array $archivos): array
    {
        $identificadoresEncontrados = [];
        $parser = new Parser();

        foreach ($archivos as $archivo) {
            if ($archivo instanceof \Illuminate\Http\UploadedFile) {

                // 1. Convertimos el PDF a texto en memoria PRIMERO (igual que hicimos con Excel)
                $pdfDocument = $parser->parseFile($archivo->path());
                $textoPlano = $pdfDocument->getText();

                // 2. Guardamos el respaldo físico en Spatie
                $espacio->addMedia($archivo)->toMediaCollection('importaciones_pdf');

                // 3. CAZADOR DE CARNETS (Regex)
                // Busca: 4 dígitos (\d{4}), un guion (-), 2 dígitos (\d{2}), un guion (-), cero o más espacios (\s*), y 1 o más dígitos (\d+)
                preg_match_all('/\b\d{4}-\d{2}-\s*\d+\b/', $textoPlano, $matchesCarnets);

                if (!empty($matchesCarnets[0])) {
                    foreach ($matchesCarnets[0] as $match) {
                        // Limpiamos los espacios (ej: "1890-19- 9521" -> "1890-19-9521")
                        $carnetLimpio = preg_replace('/\s+/', '', $match);
                        $identificadoresEncontrados[] = ['tipo' => 'carnet', 'valor' => $carnetLimpio];
                    }
                }

                // 4. CAZADOR DE CORREOS (Regex) - Como plan B
                // Busca la estructura típica de cualquier correo electrónico
                preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $textoPlano, $matchesCorreos);

                if (!empty($matchesCorreos[0])) {
                    foreach ($matchesCorreos[0] as $match) {
                        $identificadoresEncontrados[] = ['tipo' => 'email', 'valor' => strtolower(trim($match))];
                    }
                }
            }
        }

        if (empty($identificadoresEncontrados)) return [];

        // 5. Consulta masiva a la BD
        $carnets = collect($identificadoresEncontrados)->where('tipo', 'carnet')->pluck('valor')->unique()->toArray();
        $emails  = collect($identificadoresEncontrados)->where('tipo', 'email')->pluck('valor')->unique()->toArray();

        return User::whereIn('carnet', $carnets)
            ->orWhereIn('email', $emails)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Extrae el texto usando AWS Rekognition y busca carnets con Regex.
     */
    private function extraerConIA(TrabajoEspacio $espacio, array $archivos): array
    {
        $identificadoresEncontrados = [];

        $rekognition = new RekognitionClient([
            'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);

        foreach ($archivos as $archivo) {
            if ($archivo instanceof \Illuminate\Http\UploadedFile) {

                // 🚀 1. PRIMERO leemos los bytes de la imagen en la memoria RAM
                $imagenBytes = file_get_contents($archivo->path());

                // 🚀 2. LUEGO guardamos el respaldo (Spatie mueve el archivo y lo desaparece del temporal)
                $espacio->addMedia($archivo)->toMediaCollection('importaciones_ia');

                try {
                    // 3. Enviamos los bytes que ya tenemos guardados en la variable a Amazon
                    $resultado = $rekognition->detectText([
                        'Image' => [
                            'Bytes' => $imagenBytes, // <-- Usamos la variable, no el path vacío
                        ],
                    ]);

                    // 4. Juntamos el texto
                    $textoPlano = '';
                    foreach ($resultado['TextDetections'] as $deteccion) {
                        if ($deteccion['Type'] === 'LINE') {
                            $textoPlano .= $deteccion['DetectedText'] . " \n";
                        }
                    }

                    $regexCarnet = '/\d{4}\s*-\s*\d{2}\s*-\s*\d+/';

                    preg_match_all($regexCarnet, $textoPlano, $matches);

                    if (!empty($matches[0])) {
                        foreach ($matches[0] as $match) {
                            $carnetLimpio = preg_replace('/[^0-9-]/', '', $match);
                            $identificadoresEncontrados[] = ['tipo' => 'carnet', 'valor' => $carnetLimpio];
                        }
                    }

                    // 6. CAZADOR DE CORREOS
                    preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $textoPlano, $matchesCorreos);
                    if (!empty($matchesCorreos[0])) {
                        foreach ($matchesCorreos[0] as $match) {
                            $identificadoresEncontrados[] = ['tipo' => 'email', 'valor' => strtolower(trim($match))];
                        }
                    }

                } catch (AwsException $e) {
                    Log::error("Error de AWS Rekognition procesando imagen: " . $e->getMessage());
                }
            }
        }

        if (empty($identificadoresEncontrados)) return [];

        $carnets = collect($identificadoresEncontrados)->where('tipo', 'carnet')->pluck('valor')->unique()->toArray();
        $emails  = collect($identificadoresEncontrados)->where('tipo', 'email')->pluck('valor')->unique()->toArray();

        return User::whereIn('carnet', $carnets)
            ->orWhereIn('email', $emails)
            ->pluck('id')
            ->toArray();
    }

}
