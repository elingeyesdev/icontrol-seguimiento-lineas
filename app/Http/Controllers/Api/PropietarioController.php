<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Propietario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PropietarioController extends Controller
{
    public function index()
    {
        return response()->json(Propietario::where('estado', '!=', 'inactivo')->get());
    }

    public function store(Request $request)
    {
        $propietario = Propietario::create($this->validar($request));

        return response()->json($propietario, 201);
    }

    public function show(string $id)
    {
        return response()->json(Propietario::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $propietario = Propietario::findOrFail($id);
        $propietario->update($this->validar($request, $propietario->id));

        return response()->json($propietario->fresh());
    }

    public function destroy(string $id)
    {
        $propietario = Propietario::findOrFail($id);
        $propietario->update(['estado' => 'inactivo']);

        return response()->json(['message' => 'Propietario desactivado correctamente']);
    }

    private function validar(Request $request, ?int $id = null): array
    {
        foreach (['correo', 'telefono', 'fecha_registro'] as $campo) {
            if ($request->input($campo) === '') {
                $request->merge([$campo => null]);
            }
        }

        return $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('propietarios', 'correo')->ignore($id),
            ],
            'ci' => [
                'required',
                'string',
                'max:20',
                Rule::unique('propietarios', 'ci')->ignore($id),
            ],
            'estado' => ['nullable', 'string', Rule::in(['activo', 'inactivo'])],
            'fecha_registro' => ['nullable', 'date'],
        ]);
    }
}
