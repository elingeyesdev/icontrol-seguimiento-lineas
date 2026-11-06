<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruta;
use App\Services\IntegridadAsignacionesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RutaController extends Controller
{
    public function __construct(protected IntegridadAsignacionesService $integridad) {}

    public function index()
    {
        return response()->json(Ruta::where('estado', '!=', 'inactivo')->get());
    }

    public function store(Request $request)
    {
        $ruta = Ruta::create($this->validar($request));

        return response()->json($ruta, 201);
    }

    public function show(string $id)
    {
        return response()->json(Ruta::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $ruta = Ruta::findOrFail($id);
        $datos = $this->validar($request);
        $this->integridad->asegurarCambioDeEstadoPermitido($ruta, $datos);
        $ruta->update($datos);

        return response()->json($ruta->fresh());
    }

    public function destroy(string $id)
    {
        $ruta = Ruta::findOrFail($id);
        $this->integridad->desactivar($ruta);

        return response()->json(['message' => 'Ruta desactivada correctamente']);
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string'],
            'sentido' => ['nullable', 'string', 'max:20'],
            'estado' => ['nullable', 'string', Rule::in(['activo', 'inactivo'])],
        ]);
    }
}
