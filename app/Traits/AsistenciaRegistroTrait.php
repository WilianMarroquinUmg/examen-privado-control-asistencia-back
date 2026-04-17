<?php

namespace App\Traits;

use App\Models\AsistenciaIntentoFallido;
use App\Models\AsistenciaSesionToma;
use App\Models\EspacioTrabajo\TrabajoEspacio;
use App\Models\User;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Smalot\PdfParser\Parser;
use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

trait AsistenciaRegistroTrait
{
    public function tieneLimiteIntentosFallidos($userId, $tomaId): bool
    {
        $cantidadStrikes = AsistenciaIntentoFallido::where('toma_asistencia_id', $tomaId)
            ->where('alumno_id', $userId)
            ->count();

        return $cantidadStrikes >= 3;
    }

    public function tomaEstaVencida(AsistenciaSesionToma $toma): bool
    {
        $fechaActual = now();
        return $fechaActual->greaterThan($toma->hora_cierre);
    }

}
