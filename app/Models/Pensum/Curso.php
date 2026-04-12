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
        return $this->belongsToMany(
            Ciclo::class,
            'ciclos_has_cursos',
            'cursos_id',
            'ciclos_id'
        );
    }

    /**
     * SCOPE 1: Traer cursos que NO están asociados a esta facultad y ciclo
     * Ideal para el SelectorPro al momento de agregar un curso nuevo.
     */
    /**
     * Trae los cursos que NO están asociados a un ciclo que pertenece a una facultad específica.
     * $facultadYCiclo viene como string "facultadId,cicloId" (ej. "1,5")
     */
    public function scopeSinAsociarACicloYFacultad(Builder $query, $facultadYCiclo)
    {
        $ids = explode('-', $facultadYCiclo);

        if (count($ids) !== 2) {
            return $query; // Si viene mal formado, regresamos la consulta sin filtrar para que no truene
        }

        $facultadId = $ids[0];
        $cicloId = $ids[1];

        return $query->whereDoesntHave('ciclos', function (Builder $queryCiclo) use ($cicloId, $facultadId) {
            $queryCiclo->where('ciclos.id', $cicloId)
                ->whereHas('facultades', function (Builder $queryFacultad) use ($facultadId) {
                    $queryFacultad->where('facultades.id', $facultadId);
                });
        });
    }

    /**
     * Trae los cursos que SÍ están asociados a un ciclo que pertenece a una facultad específica.
     * $facultadYCiclo viene como string "facultadId,cicloId" (ej. "1,5")
     */
    public function scopeAsociadosACicloYFacultad(Builder $query, $facultadYCiclo)
    {
        // 1. Separamos el string por la coma
        $ids = explode('-', $facultadYCiclo);

        // 2. Validamos que vengan exactamente los dos datos (Facultad y Ciclo)
        if (count($ids) !== 2) {
            return $query; // Si viene mal formado, regresamos la consulta sin alterar
        }
        $facultadId = $ids[0];
        $cicloId = $ids[1];

        // 3. Eloquent puro: "Trae el curso si tiene este ciclo asociado a esta facultad"
        return $query->whereHas('ciclos', function (Builder $queryCiclo) use ($cicloId, $facultadId) {

            // Verificamos que el curso esté en este ciclo específico
            $queryCiclo->where('ciclos.id', $cicloId)

                // Y además verificamos que ese ciclo pertenezca a la facultad específica
                ->whereHas('facultades', function (Builder $queryFacultad) use ($facultadId) {
                    $queryFacultad->where('facultades.id', $facultadId);
                });
        });
    }


}
