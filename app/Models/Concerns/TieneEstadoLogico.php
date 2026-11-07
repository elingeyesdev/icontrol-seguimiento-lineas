<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait TieneEstadoLogico
{
    /**
     * Desactiva lógicamente el registro.
     */
    public function desactivar(): bool
    {
        return $this->update(['estado' => 'inactivo']);
    }

    /**
     * Activa el registro.
     */
    public function activar(): bool
    {
        return $this->update(['estado' => 'activo']);
    }

    /**
     * Accessor para determinar si el modelo está activo.
     */
    protected function estaActivo(): Attribute
    {
        return Attribute::get(fn () => in_array($this->estado, ['activo', 'disponible']));
    }
}
