<?php

namespace App\Models;


use App\Traits\Admin\ConfiguracionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * 
 *
 * @property int $id
 * @property string $key
 * @property string $value
 * @property string $descripcion
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Configuracion withoutTrashed()
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @mixin \Eloquent
 */
class Configuracion extends Model implements HasMedia
{
    use ConfiguracionTrait;
    use SoftDeletes;
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'configuraciones';


    protected $fillable = [
        'key',
        'value',
        'descripcion'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'key' => 'string',
        'value' => 'string',
        'descripcion' => 'string',
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
        'key' => 'required|string|max:255|unique:configuraciones,key',
        'value' => 'required|string|max:255',
        'descripcion' => 'required|string',
    ];

    const NOMBRE_APLICACION = 1;
    const EMAIL_APLICACION = 2;
    const TELEFONO_APLICACION = 3;
    const ESLOGAN = 4;
    const FONDO_LOGIN_TEMA_CLARO = 5;
    const FONDO_LOGIN_TEMA_OSCURO = 6;
    const LOGO = 7;

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


}
