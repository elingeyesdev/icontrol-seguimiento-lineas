<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PropietarioRequest extends FormRequest
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
        $id = $this->route('propietario');

        return [
            'estado' => 'required|in:activo,inactivo',
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('propietarios', 'user_id')->ignore($id),
            ],
        ];
    }

    /**
     * Mensajes de error personalizados en español.
     */
    public function messages(): array
    {
        return [
            'ci.unique' => 'Este CI ya está registrado en otro propietario.',
            'correo.unique' => 'Este correo ya está registrado en otro propietario.',
            'correo.email' => 'El correo debe ser una dirección válida.',
        ];
    }
}
