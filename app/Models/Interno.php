<?php

namespace App\Models;

use App\Models\Concerns\TieneEstadoLogico;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Interno extends Model
{
    use TieneEstadoLogico;

    protected $perPage = 20;

    protected $table = 'interno';

    protected $fillable = ['numero_interno', 'fecha_ingreso', 'observaciones', 'estado'];

    public function micro()
    {
        return $this->hasOne(Micro::class, 'interno_id', 'id');
    }

    protected function estadoBadge(): Attribute
    {
        return Attribute::get(function () {
            $config = [
                'disponible' => ['bg' => '#e6f4ea', 'color' => '#1e7e34', 'label' => 'Disponible'],
                'asignado' => ['bg' => '#e8f0fe', 'color' => '#1565c0', 'label' => 'Asignado'],
                'inactivo' => ['bg' => '#f0f0f0', 'color' => '#6c757d', 'label' => 'Inactivo'],
            ];

            return $config[$this->estado] ?? $config['inactivo'];
        });
    }
}
