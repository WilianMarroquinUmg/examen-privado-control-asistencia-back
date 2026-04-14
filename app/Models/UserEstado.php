<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 
 *
 * @property int $id
 * @property string $nombre
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserEstado withoutTrashed()
 * @mixin \Eloquent
 */
class UserEstado extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'users_estados';

    protected $fillable = [
        'nombre'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
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
    public static $rules = [
        'nombre' => 'required|string|max:255',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    CONST ACTIVO = 1;
    CONST INACTIVO = 2;
    CONST BLOQUEADO = 3;


}
