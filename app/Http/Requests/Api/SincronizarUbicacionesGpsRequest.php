<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SincronizarUbicacionesGpsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asignacion_turno_id' => ['required', 'integer', 'exists:asignacion_turnos,id'],
            'ubicaciones' => ['required', 'array', 'min:1', 'max:300'],
            'ubicaciones.*.fecha_hora_gps' => ['required', 'date'],
            'ubicaciones.*.latitud' => ['required', 'numeric', 'between:-90,90'],
            'ubicaciones.*.longitud' => ['required', 'numeric', 'between:-180,180'],
            'ubicaciones.*.velocidad' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
