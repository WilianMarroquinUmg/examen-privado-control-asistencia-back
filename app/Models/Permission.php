<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $subject
 * @property string $guard_name
 * @property int|null $created_at
 * @property int|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @property-read mixed $name_y_subject
 * @mixin \Eloquent
 */
class Permission extends Model
{

    const GUARD_NAME_ACTUAL = 'web';

    use HasFactory;

    protected $table = 'permissions';


    protected $fillable =
        [
            'name',
            'subject',
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
        'subject' => 'string',
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
        'name' => 'required|string|max:255|unique:permissions,name',
        'subject' => 'required|string|max:255',
    ];

    /**
     * Validation rules updated
     *
     * @var array
     */
    public static $rulesUpdated = [
        'name' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    /**
     * Custom messages for validation updated
     *
     * @var array
     */
    protected $appends = [
        'name_y_subject'
    ];

    /**
     * Accessor for relationships
     *
     * @var array
     */

    public function getNameYSubjectAttribute()
    {

        return $this->name . ' - ' . $this->subject;

    }


}
