<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoLogico;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parada extends Model
{
    use HasFactory, TieneEstadoLogico;

    protected $table = 'paradas';

    protected $fillable = [
        'nombre',
        'referencia',
        'latitud',
        'longitud',
        'estado',
    ];

    public function rutas()
    {
        return $this->belongsToMany(Ruta::class, 'parada_ruta', 'parada_id', 'ruta_id')
            ->withPivot(['orden', 'sentido', 'estado'])
            ->withTimestamps();
    }
}
