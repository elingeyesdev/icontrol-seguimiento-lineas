<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoLogico;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Propietario
 *
 * @property $id
 * @property $nombre
 * @property $apellido
 * @property $telefono
 * @property $correo
 * @property $ci
 * @property $estado
 * @property $fecha_registro
 * @property $created_at
 * @property $updated_at
 * @property Micro[] $micros
 *
 * @mixin Builder
 */
class Propietario extends Model
{
    use TieneEstadoLogico;

    protected $perPage = 20;

    protected $table = 'propietarios';

    protected $fillable = ['user_id', 'nombre', 'apellido', 'telefono', 'correo', 'ci', 'estado', 'fecha_registro'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNombreAttribute($value): ?string
    {
        return $this->user?->name ?? $value;
    }

    public function getApellidoAttribute($value): ?string
    {
        return $this->user?->apellido ?? $value;
    }

    public function getTelefonoAttribute($value): ?string
    {
        return $this->user?->telefono ?? $value;
    }

    public function getCorreoAttribute($value): ?string
    {
        return $this->user?->email ?? $value;
    }

    public function getCiAttribute($value): ?string
    {
        return $this->user?->ci ?? $value;
    }

    public function micros(): HasMany
    {
        return $this->hasMany(Micro::class, 'propietario_id', 'id');
    }
}
