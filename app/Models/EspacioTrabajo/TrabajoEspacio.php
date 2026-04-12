<?php

namespace App\Models\EspacioTrabajo;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
 * @mixin \Eloquent
 */
class TrabajoEspacio extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'trabajo_espacios';


    protected $fillable =
        [
    'catedratico_id',
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
    public function ciclo()
    {
    return $this->belongsTo(Ciclo::class,'ciclo_id','id');
    }

    public function curso()
    {
    return $this->belongsTo(Curso::class,'curso_id','id');
    }

    public function facultade()
    {
    return $this->belongsTo(Facultade::class,'facultad_id','id');
    }

    public function user()
    {
    return $this->belongsTo(User::class,'catedratico_id','id');
    }

}
