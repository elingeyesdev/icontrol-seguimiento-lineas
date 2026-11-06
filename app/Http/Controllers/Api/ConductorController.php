<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreConductorRequest;
use App\Models\Conductor;
use App\Models\User;
use App\Services\IntegridadAsignacionesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ConductorController extends Controller
{
    public function __construct(protected IntegridadAsignacionesService $integridad) {}

    public function index()
    {
        return response()->json(Conductor::where('estado', '!=', 'inactivo')->get());
    }

    public function store(StoreConductorRequest $request)
    {
        $conductor = Conductor::create($request->validated());

        return response()->json($conductor, 201);
    }

    public function show(string $id)
    {
        return response()->json(Conductor::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $conductor = Conductor::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:30',
            'apellido' => 'required|string|max:30',
            'telefono' => 'required|string|max:15',
            'correo' => [
                'required',
                'email',
                'max:50',
                Rule::unique('conductor', 'correo')->ignore($conductor->id),
            ],
            'ci' => [
                'required',
                'string',
                'max:20',
                Rule::unique('conductor', 'ci')->ignore($conductor->id),
            ],
            'estado' => 'required|in:activo,inactivo',
        ]);

        $this->integridad->asegurarCambioDeEstadoPermitido($conductor, $validated);

        $conductor->update($validated);

        return response()->json($conductor);
    }

    public function destroy(string $id)
    {
        $conductor = Conductor::findOrFail($id);
        $this->integridad->desactivar($conductor);

        return response()->json(['message' => 'Conductor desactivado correctamente']);
    }

    /**
     * Vincula manualmente una ficha de conductor con una cuenta de usuario.
     * Útil cuando el admin crea la cuenta y la ficha por separado.
     */
    public function vincularUsuario(Request $request, string $id)
    {
        $conductor = Conductor::findOrFail($id);

        $datos = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'email' => ['nullable', 'email', 'exists:users,email'],
        ]);

        if (empty($datos['user_id']) && empty($datos['email'])) {
            return response()->json([
                'message' => 'Debe indicar user_id o email.',
            ], 422);
        }

        $user = ! empty($datos['user_id'])
            ? User::findOrFail($datos['user_id'])
            : User::where('email', $datos['email'])->firstOrFail();

        $ocupado = Conductor::where('user_id', $user->id)
            ->whereKeyNot($conductor->id)
            ->exists();

        if ($ocupado) {
            return response()->json([
                'message' => 'El usuario ya está vinculado a otro conductor.',
            ], 422);
        }

        $conductor->update(['user_id' => $user->id]);
        $user->assignRole('conductor');

        return response()->json([
            'message' => 'Conductor vinculado correctamente.',
            'conductor' => $conductor->fresh(['user']),
        ]);
    }
}
