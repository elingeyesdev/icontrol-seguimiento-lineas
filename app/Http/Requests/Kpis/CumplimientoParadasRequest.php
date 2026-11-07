<?php

namespace App\Http\Requests\Kpis;

use Illuminate\Foundation\Http\FormRequest;

class CumplimientoParadasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'ruta_id' => ['nullable', 'integer', 'exists:ruta,id'],
            'conductor_id' => ['nullable', 'integer', 'exists:conductor,id'],
            'micro_id' => ['nullable', 'integer', 'exists:micro,id'],
            'turno_id' => ['nullable', 'integer', 'exists:turno,id'],
            'sentido' => ['nullable', 'in:Ida,Vuelta'],
        ];
    }
}
