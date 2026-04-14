<?php

namespace App\Models\EspacioTrabajo;


use App\Models\AsistenciaConfiguracion;
use App\Models\AsistenciaSesion;
use App\Models\Pensum\Ciclo;
use App\Models\Pensum\Curso;
use App\Models\Pensum\Facultad;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 *
 *
 * @property int $id
 * @property int $catedratico_id
 * @property int $facultad_id
 * @property int $ciclo_id
 * @property int $curso_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio whereCatedraticoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio whereCicloId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio whereCursoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio whereFacultadId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrabajoEspacio withoutTrashed()
 * @property string $estado
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $alumnos
 * @property-read int|null $alumnos_count
 * @property-read User $catedratico
 * @property-read Ciclo $ciclo
 * @property-read Curso $curso
 * @property-read Facultad $facultad
 * @property-read int $cantidad_alumnos
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AsistenciaSesion> $sesiones
 * @property-read int|null $sesiones_count
 * @method static Builder<static>|TrabajoEspacio buscar($busqueda)
 * @method static Builder<static>|TrabajoEspacio whereEstado($value)
 * @mixin \Eloquent
 */
class TrabajoEspacio extends Model implements HasMedia
{

    use SoftDeletes;
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'trabajo_espacios';


    protected $fillable = [
            'catedratico_id',
            'estado',
            'facultad_id',
            'ciclo_id',
            'curso_id'
        ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
            'id' => 'integer',
            'catedratico_id' => 'integer',
            'facultad_id' => 'integer',
            'ciclo_id' => 'integer',
            'curso_id' => 'integer',
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
            'catedratico_id' => 'required|integer',
            'facultad_id' => 'required|integer',
            'ciclo_id' => 'required|integer',
            'curso_id' => 'required|integer',
        ];

    protected $appends = ['cantidad_alumnos'];

    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    const PENDIENTE = 'Pendiente';
    const ACTIVO = 'Activo';
    const FINALIZADO = 'Finalizado';

    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'ciclo_id', 'id');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'id');
    }

    public function facultad(): BelongsTo
    {
        return $this->belongsTo(Facultad::class, 'facultad_id', 'id');
    }

    public function catedratico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'catedratico_id', 'id');
    }

    public function alumnos(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'trabajo_espacios_has_alumnos',
            'trabajo_espacios_id',
            'users_id'
        );
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(AsistenciaSesion::class, 'espacio_id', 'id');
    }

    public function scopeBuscar(Builder $query, $busqueda)
    {
        if (!empty($busqueda)) {
            $query->whereHas('ciclo', function (Builder $q) use ($busqueda) {
                $q->where('nombre', 'like', '%' . $busqueda . '%');
            })->orWhereHas('curso', function (Builder $q) use ($busqueda) {
                $q->where('nombre', 'like', '%' . $busqueda . '%');
            })->orWhereHas('facultad', function (Builder $q) use ($busqueda) {
                $q->where('nombre', 'like', '%' . $busqueda . '%');
            });
        }
    }

    public function getCantidadAlumnosAttribute(): int
    {
        return $this->alumnos()->count();
    }
}
