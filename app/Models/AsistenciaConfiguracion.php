<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $requiere_ubicacion
 * @property int $requiere_prueba_vida
 * @property int $requiere_codigo_otp
 * @property string $cantidad_tomas_requeridas
 * @property int $minutos_tolerancia
 * @property int $catedratico_id
 * @property int $espacio_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereCantidadTomasRequeridas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereCatedraticoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereEspacioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereMinutosTolerancia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereRequiereCodigoOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereRequierePruebaVida($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereRequiereUbicacion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaConfiguracion withoutTrashed()
 * @mixin \Eloquent
 */
class AsistenciaConfiguracion extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'asistencia_configuraciones';


    protected $fillable =
        [
    'requiere_ubicacion',
    'requiere_prueba_vida',
    'requiere_codigo_otp',
    'cantidad_tomas_requeridas',
    'minutos_tolerancia',
    'catedratico_id',
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
        'requiere_ubicacion' => 'integer',
        'requiere_prueba_vida' => 'integer',
        'requiere_codigo_otp' => 'integer',
        'cantidad_tomas_requeridas' => 'string',
        'minutos_tolerancia' => 'integer',
        'catedratico_id' => 'integer',
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
    'requiere_ubicacion' => 'required|integer',
    'requiere_prueba_vida' => 'required|integer',
    'requiere_codigo_otp' => 'required|integer',
    'cantidad_tomas_requeridas' => 'required|string|max:45',
    'minutos_tolerancia' => 'required|integer',
    'catedratico_id' => 'required|integer',
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

    public function user()
    {
    return $this->belongsTo(User::class,'catedratico_id','id');
    }

}
