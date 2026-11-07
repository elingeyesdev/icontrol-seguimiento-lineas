<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parada;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParadaController extends Controller
{
    public function index()
    {
        return response()->json(Parada::where('estado', '!=', 'inactivo')->get());
    }

    public function store(Request $request)
    {
        $parada = Parada::create($this->validar($request));

        return response()->json($parada, 201);
    }

    public function show(string $id)
    {
        return response()->json(Parada::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $parada = Parada::findOrFail($id);
        $parada->update($this->validar($request));

        return response()->json($parada->fresh());
    }

    public function destroy(string $id)
    {
        $parada = Parada::findOrFail($id);
        $parada->update(['estado' => 'inactivo']);

        return response()->json(['message' => 'Parada desactivada correctamente']);
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'referencia' => ['nullable', 'string', 'max:255'],
            'latitud' => ['nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['nullable', 'numeric', 'between:-180,180'],
            'estado' => ['nullable', 'string', Rule::in(['activo', 'inactivo'])],
        ]);
    }
}
