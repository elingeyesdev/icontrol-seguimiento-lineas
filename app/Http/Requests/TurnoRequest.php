<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * TurnoRequest - Validación para el catálogo estático de Turnos.
 * Nota: el TurnoController valida inline, este FormRequest
 * queda disponible como referencia o para uso futuro.
 */
class TurnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'in:mañana,tarde,noche'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'in:activo,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Selecciona un nombre de turno.',
            'nombre.in' => 'El turno debe ser: mañana, tarde o noche.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
        ];
    }
}
