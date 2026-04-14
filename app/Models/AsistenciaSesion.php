<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $fecha
 * @property string $estado
 * @property int $espacio_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion whereEspacioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion withoutTrashed()
 * @mixin \Eloquent
 */
class AsistenciaSesion extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'asistencia_sesiones';


    protected $fillable =
        [
    'fecha',
    'estado',
    'espacio_id'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'fecha' => 'date',
        'estado' => 'string',
        'espacio_id' => 'integer',
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
    'fecha' => 'required|date',
    'estado' => 'required|string',
    'espacio_id' => 'required|integer',
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
    public function trabajoEspacio()
    {
    return $this->belongsTo(TrabajoEspacio::class,'espacio_id','id');
    }

}
