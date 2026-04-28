<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    use HasFactory;

    protected $table = 'conductor';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'correo',
        'ci',
    ];
}
