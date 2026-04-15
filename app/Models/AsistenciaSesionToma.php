<?php

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $hora_apertura
 * @property \Illuminate\Support\Carbon $hora_cierre
 * @property string|null $codito_otp
 * @property int $numero_toma
 * @property int $sesion_id
 * @property float|null $longitud_origen
 * @property float|null $latitud_origen
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereCoditoOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereHoraApertura($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereHoraCierre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereLatitudOrigen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereLongitudOrigen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereNumeroToma($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereSesionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesionToma withoutTrashed()
 * @mixin \Eloquent
 */
class AsistenciaSesionToma extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'asistencia_sesion_tomas';


    protected $fillable = [
        'hora_apertura',
        'hora_cierre',
        'codito_otp',
        'numero_toma',
        'sesion_id',
        'longitud_origen',
        'latitud_origen',
        'radio_metros',
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */

    protected $casts = [
        'id' => 'integer',
        'hora_apertura' => 'datetime',
        'hora_cierre' => 'datetime',
        'codito_otp' => 'string',
        'numero_toma' => 'integer',
        'sesion_id' => 'integer',
        'longitud_origen' => 'float',
        'latitud_origen' => 'float',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'hora_apertura' => 'required|date',
        'hora_cierre' => 'required|date',
        'codito_otp' => 'nullable|string|max:10',
        'numero_toma' => 'required|integer',
        'sesion_id' => 'required|integer',
        'longitud_origen' => 'nullable|numeric',
        'latitud_origen' => 'nullable|numeric',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function sesion(): BelongsTo
    {
        return $this->belongsTo(AsistenciaSesion::class, 'sesion_id', 'id');
    }

    public function registros(): HasMany
    {
        return $this->hasMany(AsistenciaRegistro::class, 'toma_asistencia_id', 'id');
    }

    public function scopeSoloActivasParaAlumno(Builder $query, $alumnoId): Builder
    {
        $ahora = Carbon::now();

        return $query->whereHas('sesion.espacio.alumnos', function ($q) use ($alumnoId) {
            $q->where('users.id', $alumnoId);
        })
            ->where('hora_apertura', '<=', $ahora)
            ->where('hora_cierre', '>=', $ahora) // Condición 2: Que el tiempo no se haya agotado
            ->whereDoesntHave('registros', function ($q) use ($alumnoId) {
                $q->where('alumno_id', $alumnoId);
            });
    }

}
