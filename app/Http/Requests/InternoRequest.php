<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InternoRequest extends FormRequest
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
        $id = $this->route('interno');

        return [
            'numero_interno' => [
                'required',
                'string',
                'max:10',
                Rule::unique('interno', 'numero_interno')->ignore($id),
            ],
            'fecha_ingreso' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado' => 'required|in:disponible,asignado,inactivo',
        ];
    }

    public function messages(): array
    {
        return [
            'numero_interno.unique' => 'Este número de interno ya se encuentra registrado.',
            'numero_interno.required' => 'El número de interno es obligatorio.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
        ];
    }
}
