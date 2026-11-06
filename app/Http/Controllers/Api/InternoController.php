<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interno;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InternoController extends Controller
{
    private const ESTADOS = ['disponible', 'asignado', 'inactivo'];

    public function index()
    {
        return response()->json(Interno::where('estado', '!=', 'inactivo')->get());
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['fecha_ingreso'] ??= now()->format('Y-m-d H:i:s');

        $interno = Interno::create($data);

        return response()->json($interno, 201);
    }

    public function show(string $id)
    {
        return response()->json(Interno::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $interno = Interno::findOrFail($id);
        $interno->update($this->validar($request, $interno->id));

        return response()->json($interno->fresh());
    }

    public function destroy(string $id)
    {
        $interno = Interno::findOrFail($id);
        $interno->update(['estado' => 'inactivo']);

        return response()->json(['message' => 'Interno desactivado correctamente']);
    }

    private function validar(Request $request, ?int $id = null): array
    {
        // La app móvil enviaba "activo"; el catálogo usa disponible/asignado/inactivo.
        if ($request->input('estado') === 'activo') {
            $request->merge(['estado' => 'disponible']);
        }

        $requerido = $id === null ? 'required' : 'sometimes';

        return $request->validate([
            'numero_interno' => [
                $requerido,
                'string',
                'max:10',
                Rule::unique('interno', 'numero_interno')->ignore($id),
            ],
            'fecha_ingreso' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
            'estado' => ['nullable', 'string', Rule::in(self::ESTADOS)],
        ]);
    }
}
