<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
 * 
 *
 * @property int $id
 * @property string $primer_nombre
 * @property string|null $segundo_nombre
 * @property string $primer_apellido
 * @property string|null $segundo_apellido
 * @property string $usuario
 * @property string|null $email
 * @property string|null $carnet
 * @property int $estado_id
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\UserEstado|null $estado
 * @property-read mixed $nombre_completo
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rol> $roles
 * @property-read int|null $roles_count
 * @method static Builder<static>|User busquedaAvanzada($termino = null)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User permission($permissions, $without = false)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static Builder<static>|User sinUsuarioIds($ids)
 * @method static Builder<static>|User whereCarnet($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereDeletedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereEstadoId($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePrimerApellido($value)
 * @method static Builder<static>|User wherePrimerNombre($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereSegundoApellido($value)
 * @method static Builder<static>|User whereSegundoNombre($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereUsuario($value)
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;

    protected $guard_name = 'web';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'usuario',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //definir reglas
    public static $rules = [
        'usuario' => 'required|string|max:255',
        'primer_nombre' => 'required|string|max:255',
        'segundo_nombre' => 'nullable|string|max:255',
        'primer_apellido' => 'required|string|max:255',
        'segundo_apellido' => 'nullable|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ];

    //definir reglas de mensajes

    public static $messages = [
        'usuario.required' => 'El usuario es requerido',
        'usuario.string' => 'El usuario debe ser una cadena de texto',
        'usuario.max' => 'El usuario no debe exceder los 255 caracteres',
        'primer_nombre.required' => 'El primer nombre es requerido',
        'primer_nombre.string' => 'El primer nombre debe ser una cadena de texto',
        'primer_nombre.max' => 'El primer nombre no debe exceder los 255 caracteres',
        'segundo_nombre.string' => 'El segundo nombre debe ser una cadena de texto',
        'segundo_nombre.max' => 'El segundo nombre no debe exceder los 255 caracteres',
        'primer_apellido.required' => 'El primer apellido es requerido',
        'primer_apellido.string' => 'El primer apellido debe ser una cadena de texto',
        'primer_apellido.max' => 'El primer apellido no debe exceder los 255 caracteres',
        'segundo_apellido.string' => 'El segundo apellido debe ser una cadena de texto',
        'segundo_apellido.max' => 'El segundo apellido no debe exceder los 255 caracteres',
        'email.required' => 'El email es requerido',
        'email.string' => 'El email debe ser una cadena de texto',
        'email.email' => 'El email debe ser un correo electrónico válido',
        'email.max' => 'El email no debe exceder los 255 caracteres',
        'email.unique' => 'El email ya se encuentra registrado',
        'password.required' => 'La contraseña es requerida',
        'password.string' => 'La contraseña debe ser una cadena de texto',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres',
    ];

    protected $appends = ['nombre_completo'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Devolver al usuario autenticado, sus roles y permisos.
     *
     * @return User
     */
    public function responseUser(): array
    {
        return [
            'id' => $this->id,
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
            'nombre_completo' => $this->nombre_completo,
            'usuario' => $this->usuario,
            'email' => $this->email,
            'estado' => $this->estado()->select('id', 'nombre')->first(),
            'roles' => $this->getRoleNames(),
            'permisos' => $this->getAllPermissions()->map(function ($permission) {
                return [
                    'accion' => $permission->name,
                    'recurso' => $permission->subject
                ];
            })->toArray(),
            'avatar_thumb24' => optional($this->getMedia('avatars')->last())->getUrl('thumb24'),
        ];
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');

    }

    public function getNombreCompletoAttribute()
    {
        return $this->primer_nombre . ' ' . $this->segundo_nombre . ' ' . $this->primer_apellido . ' ' . $this->segundo_apellido;

    }

    public function registerMediaConversions(Media $media = null): void
    {
        if ($media?->collection_name === 'avatars') {
            $this->addMediaConversion('thumb24')
                ->width(75)
                ->height(75)
                ->nonQueued();
        }
    }

    public function estado()
    {
        return $this->hasOne(UserEstado::class, 'id', 'estado_id');

    }

    /**
     * Scope para buscar usuarios por nombres, apellidos, email o carnet.
     */
    public function scopeBusquedaAvanzada(Builder $query, $termino = null)
    {
        if (empty($termino)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($termino) {
            $q->where('primer_nombre', 'LIKE', "%{$termino}%")
                ->orWhere('segundo_nombre', 'LIKE', "%{$termino}%")
                ->orWhere('primer_apellido', 'LIKE', "%{$termino}%")
                ->orWhere('segundo_apellido', 'LIKE', "%{$termino}%")
                ->orWhere('email', 'LIKE', "%{$termino}%")
                ->orWhere('carnet', 'LIKE', "%{$termino}%")

                ->orWhereRaw("CONCAT_WS(' ', primer_nombre, segundo_nombre, primer_apellido, segundo_apellido) LIKE ?", ["%{$termino}%"]);
        });
    }

    /**
     * Excluye usuarios por ID pasados como un string separado por guiones ("1-2-3")
     */
    public function scopeSinUsuarioIds(Builder $query, $ids)
    {
        if (empty($ids)) {
            return $query;
        }

        $arregloIds = is_string($ids) ? explode('-', $ids) : $ids;

        return $query->whereNotIn('id', $arregloIds);
    }

}
