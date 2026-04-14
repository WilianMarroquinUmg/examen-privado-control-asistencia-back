<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $id
 * @property string $hora_registro
 * @property string|null $latitud
 * @property string|null $longitud
 * @property string|null $foto_evidencia_url
 * @property float|null $aws_liveness_score
 * @property int $fue_aprobada
 * @property int $toma_asistencia_id
 * @property int $alumno_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\AsistenciaSesionToma $asistenciaSesionToma
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereAlumnoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereAwsLivenessScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereFotoEvidenciaUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereFueAprobada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereHoraRegistro($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereLatitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereLongitud($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereTomaAsistenciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaRegistro withoutTrashed()
 * @mixin \Eloquent
 */
class AsistenciaRegistro extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'asistencia_registros';


    protected $fillable =
        [
    'hora_registro',
    'latitud',
    'longitud',
    'foto_evidencia_url',
    'aws_liveness_score',
    'fue_aprobada',
    'toma_asistencia_id',
    'alumno_id'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'hora_registro' => 'string',
        'latitud' => 'string',
        'longitud' => 'string',
        'foto_evidencia_url' => 'string',
        'aws_liveness_score' => 'float',
        'fue_aprobada' => 'integer',
        'toma_asistencia_id' => 'integer',
        'alumno_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
    [
    'hora_registro' => 'required|string',
    'latitud' => 'nullable|string|max:50',
    'longitud' => 'nullable|string|max:50',
    'foto_evidencia_url' => 'nullable|string|max:120',
    'aws_liveness_score' => 'nullable|numeric',
    'fue_aprobada' => 'required|integer',
    'toma_asistencia_id' => 'required|integer',
    'alumno_id' => 'required|integer',
];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function toma(): BelongsTo
    {
    return $this->belongsTo(AsistenciaSesionToma::class,'toma_asistencia_id','id');
    }

    public function alumno(): BelongsTo
    {
    return $this->belongsTo(User::class,'alumno_id','id');
    }

}
