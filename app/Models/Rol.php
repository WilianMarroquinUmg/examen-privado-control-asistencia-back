<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property int|null $created_at
 * @property int|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rol whereUpdatedAt($value)
 * @mixin \Eloquent
 */

use Spatie\Permission\Models\Role as SpatieRole;

class Rol extends SpatieRole
{

    use HasFactory;

    protected $table = 'roles';

    const ADMIN = 1;
    const EMPLEADO = 2;
    const PROGRAMADOR = 3;
    const ESTUDIANTE = 4;
    const CATEDRATICO = 5;

    const GUARD_NAME_ACTUAL = 'web';
    protected $fillable = [
        'name',
        'guard_name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'guard_name' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|string|max:255|unique:roles,name',
    ];

    public static $rulesUpdated = [
        'name' => 'required|string|max:255',
    ];


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
