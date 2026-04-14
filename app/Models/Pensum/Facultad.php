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
 * @property string $codigo
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad whereCodigo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facultad withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pensum\Ciclo> $ciclos
 * @property-read int|null $ciclos_count
 * @mixin \Eloquent
 */
class Facultad extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'facultades';


    protected $fillable =
        [
    'nombre',
    'codigo'
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
    'nombre' => 'required|string|max:100',
    'codigo' => 'required|string|max:20',
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

    public function ciclos(): BelongsToMany
    {
        return $this->belongsToMany(
            Ciclo::class,
            'facultades_has_ciclos',
            'facultades_id',
            'ciclos_id'
        );

    }

}
