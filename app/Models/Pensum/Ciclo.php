<?php

namespace App\Models\Pensum;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $nombre
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pensum\Curso> $cursos
 * @property-read int|null $cursos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pensum\Facultad> $facultades
 * @property-read int|null $facultades_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo asociadosAFacultad($facultadId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ciclo sinAsociarAFacultad($facultadId)
 * @mixin \Eloquent
 */
class Ciclo extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'ciclos';


    protected $fillable =
        [
            'nombre'
        ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
            'id' => 'integer',
            'nombre' => 'string',
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
            'nombre' => 'required|string|max:50',
        ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    public function facultades(): BelongsToMany
    {
        return $this->belongsToMany(
            Facultad::class,
            'facultades_has_ciclos',
            'ciclos_id',
            'facultades_id'
        );
    }

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(
            Curso::class,
            'ciclos_has_cursos',
            'ciclo_id',
            'curso_id'
        )->withPivot('facultad_id');
    }

    public function scopeSinAsociarAFacultad($query, $facultadId)
    {
        return $query->whereDoesntHave('facultades', function ($q) use ($facultadId) {
            $q->where('facultades_id', $facultadId);
        });

    }

    public function scopeAsociadosAFacultad($query, $facultadId)
    {
        return $query->whereHas('facultades', function ($q) use ($facultadId) {
            $q->where('facultades_id', $facultadId);
        });

    }

    /**
     * Accessor for relationships
     *
     * @var array
     */


}
