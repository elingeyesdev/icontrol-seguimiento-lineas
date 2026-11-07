<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Micro;
use App\Services\IntegridadAsignacionesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MicroController extends Controller
{
    public function __construct(protected IntegridadAsignacionesService $integridad) {}

    public function index()
    {
        return response()->json(Micro::where('estado', '!=', 'inactivo')->get());
    }

    public function store(Request $request)
    {
        $micro = Micro::create($this->validar($request));

        return response()->json($micro, 201);
    }

    public function show(string $id)
    {
        return response()->json(Micro::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $micro = Micro::findOrFail($id);
        $datos = $this->validar($request, $micro->id);
        $this->integridad->asegurarCambioDeEstadoPermitido($micro, $datos);
        $micro->update($datos);

        return response()->json($micro->fresh());
    }

    public function destroy(string $id)
    {
        $micro = Micro::findOrFail($id);
        $this->integridad->desactivar($micro);

        return response()->json(['message' => 'Micro desactivado correctamente']);
    }

    private function validar(Request $request, ?int $id = null): array
    {
        $requerido = $id === null ? 'required' : 'sometimes';

        return $request->validate([
            'propietario_id' => [$requerido, 'integer', 'exists:propietarios,id'],
            'interno_id' => ['nullable', 'integer', 'exists:interno,id'],
            'placa' => [
                $requerido,
                'string',
                'max:20',
                Rule::unique('micro', 'placa')->ignore($id),
            ],
            'chasis' => ['nullable', 'string', 'max:50'],
            'anio_fabricacion' => ['nullable', 'integer', 'between:1900,'.(now()->year + 1)],
            'modelo' => [$requerido, 'string', 'max:30'],
            'marca' => [$requerido, 'string', 'max:30'],
            'capacidad_pasajeros' => [$requerido, 'integer', 'min:1', 'max:200'],
            'estado' => ['nullable', 'string', Rule::in(['activo', 'inactivo'])],
        ]);
    }
}
