<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MicroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('micro')?->id ?? $this->route('micro');

        return [
            'propietario_id' => 'required|exists:propietarios,id',
            'interno_id' => [
                'nullable',
                'exists:interno,id',
                Rule::unique('micro', 'interno_id')->ignore($id),
            ],
            'placa' => [
                'required',
                'string',
                'max:20',
                Rule::unique('micro', 'placa')->ignore($id),
            ],
            'chasis' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('micro', 'chasis')->ignore($id),
            ],
            'anio_fabricacion' => 'nullable|integer|min:1950|max:'.(date('Y') + 1),
            'modelo' => 'required|string|max:30',
            'marca' => 'required|string|max:30',
            'capacidad_pasajeros' => 'required|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    public function messages(): array
    {
        return [
            'placa.unique' => 'Esta placa ya está registrada en otro micro.',
            'chasis.unique' => 'Este número de chasis ya se encuentra registrado.',
            'interno_id.unique' => 'Este número interno ya está asignado a otro micro.',
            'propietario_id.required' => 'Debe seleccionar un propietario.',
            'propietario_id.exists' => 'El propietario seleccionado no es válido.',
        ];
    }
}
