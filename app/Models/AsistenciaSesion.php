<?php

namespace App\Models;


use App\Models\EspacioTrabajo\TrabajoEspacio;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property int $configuracion_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenciaSesion whereConfiguracionId($value)
 * @mixin \Eloquent
 */
class AsistenciaSesion extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'asistencia_sesiones';


    protected $fillable = [
        'fecha',
        'espacio_id',
        'configuracion_id'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'fecha' => 'date',
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
    public static $rules = [
        'fecha' => 'required|date',
        'espacio_id' => 'required|integer',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    const PENDIENTE = 'Pendiente';
    const EN_CURSO = 'En curso';
    const FINALIZADA = 'Finalizada';


    protected function estado(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$this->relationLoaded('tomas') || !$this->relationLoaded('configuration')) {
                    return $value;
                }

                $tomasRealizadas = $this->tomas->count();
                $tomasRequeridas = (int) $this->configuration->cantidad_tomas_requeridas;

                if ($tomasRealizadas === 0) {
                    return 'Pendiente';
                }

                $ultimaToma = $this->tomas->sortByDesc('id')->first();
                $ultimaFinalizada = $ultimaToma && $ultimaToma->estado === 'Finalizada';

                if ($tomasRealizadas == $tomasRequeridas && $ultimaFinalizada) {
                    return 'Finalizada';
                }

                return 'En curso';
            }
        );
    }

    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function espacio(): BelongsTo
    {
        return $this->belongsTo(TrabajoEspacio::class, 'espacio_id', 'id');
    }

    public function configuration(): BelongsTo
    {
        return $this->belongsTo(AsistenciaConfiguracion::class, 'configuracion_id', 'id');

    }

    public function tomas(): HasMany
    {
        return $this->hasMany(AsistenciaSesionToma::class, 'sesion_id', 'id');

    }

}
