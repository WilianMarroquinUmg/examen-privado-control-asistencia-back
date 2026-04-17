<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenciaIntentoFallido extends Model
{

    protected $table = 'asistencia_intentos_fallidos';

    protected $fillable = [
        'alumno_id',
        'toma_asistencia_id',
        'motivo',
    ];
}
