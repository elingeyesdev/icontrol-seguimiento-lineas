<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Conductor extends Model
{
    use HasApiTokens,HasFactory;

    protected $table = 'conductor';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'correo',
        'ci',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function internos()
    {
        return $this->belongsToMany(\App\Models\Interno::class, 'conductor_interno', 'conductor_id', 'interno_id');
    }
}
