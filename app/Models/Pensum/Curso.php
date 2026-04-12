<?php

namespace App\Models\Pensum;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 *
 * @property int $id
 * @property string $nombre
 * @property string $codigo
 * @property string $identificacion_institucional
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso whereIdentificacionInstitucional($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Curso withoutTrashed()
 * @mixin \Eloquent
 */
class Curso extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'cursos';


    protected $fillable =
        [
    'nombre',
    'codigo',
    'identificacion_institucional'
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
        'codigo' => 'string',
        'identificacion_institucional' => 'string',
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
    'nombre' => 'required|string|max:150',
    'codigo' => 'required|string|max:45',
    'identificacion_institucional' => 'required|string|max:60',
];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];

    public function ciclos(): BelongsToMany
    {
        return $this->belongsToMany(Ciclo::class, 'ciclos_has_cursos', 'curso_id', 'ciclo_id')
            ->withPivot('facultad_id'); // 🚀 Esto es clave para poder filtrar por facultad
    }

    /**
     * Trae los cursos que NO están asociados a un ciclo que pertenece a una facultad específica.
     * $facultadYCiclo viene como string "facultadId,cicloId" (ej. "1,5")
     */
    public function scopeSinAsociarACicloYFacultad(Builder $query, $facultadYCiclo)
    {
        $ids = explode('-', $facultadYCiclo);

        if (count($ids) !== 2) {
            return $query;
        }

        $facultadId = $ids[0];
        $cicloId = $ids[1];

        return $query->whereDoesntHave('ciclos', function ($q) use ($cicloId, $facultadId) {
            $q->where('ciclo_id', $cicloId)
            ->where('facultad_id', $facultadId);
        });
    }

    /**
     * Trae los cursos que SÍ están asociados a un ciclo que pertenece a una facultad específica.
     * $facultadYCiclo viene como string "facultadId,cicloId" (ej. "1,5")
     */
    public function scopeAsociadosACicloYFacultad(Builder $query, $facultadYCiclo)
    {
        $ids = explode('-', $facultadYCiclo);

        if (count($ids) !== 2) return $query;

        $facultadId = $ids[0];
        $cicloId = $ids[1];

        // Usamos whereHas para filtrar por la tabla pivote
        return $query->whereHas('ciclos', function ($q) use ($cicloId, $facultadId) {
            $q->where('ciclo_id', $cicloId) // Filtramos por el ID del ciclo
            ->where('facultad_id', $facultadId); // 🎯 Filtramos por la facultad en la misma tabla
        });
    }


}
