<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoLogico;
use Illuminate\Database\Eloquent\Model;

class Micro extends Model
{
    use TieneEstadoLogico;

    protected $perPage = 20;

    protected $table = 'micro';

    protected $fillable = [
        'propietario_id',
        'interno_id',
        'placa',
        'chasis',
        'anio_fabricacion',
        'modelo',
        'marca',
        'capacidad_pasajeros',
        'estado',
    ];

    public function propietario()
    {
        return $this->belongsTo(Propietario::class, 'propietario_id', 'id');
    }

    public function interno()
    {
        return $this->belongsTo(Interno::class, 'interno_id', 'id');
    }
}
