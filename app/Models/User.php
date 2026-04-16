<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
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
 * @property-read bool $tiene_foto_certificada // 🚀 Añadido al PHPDoc
 * @property-read Media|null $fotoCertificada // 🚀 Añadido al PHPDoc
 * @property-read Media|null $fotoPendienteCertificacion // 🚀 Añadido al PHPDoc
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Rol> $roles
 * @property-read int|null $roles_count
 * @method static Builder<static>|User busquedaAvanzada($termino = null)
 * @method static Builder<static>|User soloPendientesDeCertificacion() // 🚀 Añadido al PHPDoc
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

    protected $fillable = [
        'usuario',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'email',
        'password',
        'carnet',
        'estado_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static $rules = [
        'usuario' => 'required|string|max:255',
        'primer_nombre' => 'required|string|max:255',
        'segundo_nombre' => 'nullable|string|max:255',
        'primer_apellido' => 'required|string|max:255',
        'segundo_apellido' => 'nullable|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
    ];

    public static $messages = [
        // ... (tus mensajes originales) ...
    ];

    // 🚀 Añadimos 'tiene_foto_certificada' para que viaje siempre en los JSON
    protected $appends = ['nombre_completo', 'tiene_foto_certificada'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function responseUser(): array
    {
        return [
            'id' => $this->id,
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
            'nombre_completo' => $this->nombre_completo,
            'tiene_foto_certificada' => $this->tiene_foto_certificada,
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

    // =========================================================================
    // 🚀 NUEVO: RELACIONES Y COMPUTADOS BIOMÉTRICOS
    // =========================================================================

    /**
     * Relación 1 a 1 polimórfica para obtener LA ÚLTIMA foto aprobada.
     */
    public function fotoCertificada()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'foto_perfil_biometrico')
            ->where('fue_certificada', 1) // Es más seguro usar 1 en bases de datos relacionales
            ->latest('id'); // 🚀 MAGIA: Ordena de mayor a menor y el morphOne saca el primero
    }

    /**
     * Relación 1 a 1 polimórfica para obtener LA ÚLTIMA foto pendiente de aprobar por el catedrático.
     */
    public function fotoPendienteCertificacion()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'foto_perfil_biometrico')
            ->where('fue_certificada', false)
            ->latestOfMany();
    }

    /**
     * Scope: Traer únicamente a los alumnos que subieron foto pero el profe no la ha revisado.
     * Ideal para llenar la tabla de la "Bandeja de Entrada" del catedrático.
     */
    public function scopeSoloPendientesDeCertificacion(Builder $query)
    {
        // whereHas hace un INNER JOIN mágico buscando a los que sí tengan relación 'fotoPendienteCertificacion'
        return $query->whereHas('fotoPendienteCertificacion');
    }

    /**
     * Atributo Computado: ¿Tiene o no tiene permiso de usar la IA?
     */
    public function getTieneFotoCertificadaAttribute(): bool
    {
        // El Escudo Protector contra N+1
        if ($this->relationLoaded('fotoCertificada')) {
            return $this->fotoCertificada !== null;
        }

        // Si no se precargó la relación, hacemos un simple COUNT (exists) a la BD
        return $this->fotoCertificada()->exists();
    }

    // =========================================================================

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

    public function scopeSinUsuarioIds(Builder $query, $ids)
    {
        if (empty($ids)) {
            return $query;
        }

        $arregloIds = is_string($ids) ? explode('-', $ids) : $ids;
        return $query->whereNotIn('id', $arregloIds);
    }

    public function asistenciaRegistros(): HasMany
    {
        return $this->hasMany(AsistenciaRegistro::class, 'alumno_id');
    }

    public function scopeSoloAlumnos(Builder $query)
    {
        return $query->whereHas('roles', function (Builder $q) {
            $q->where('name', 'Estudiante');
        });
    }
}
