<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use App\Models\User;
use App\Services\IntegridadAsignacionesService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ConductorController extends Controller
{
    public function __construct(protected IntegridadAsignacionesService $integridad)
    {
        $this->middleware('permission:conductor.index')->only('index');
        $this->middleware('permission:conductor.create')->only(['create', 'store']);
        $this->middleware('permission:conductor.show')->only('show');
        $this->middleware('permission:conductor.edit')->only(['edit', 'update']);
        $this->middleware('permission:conductor.destroy')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $conductores = Conductor::where('estado', '!=', 'inactivo')
            ->when($buscar, function ($query, $buscar) {
                return $query->where(function ($subQuery) use ($buscar) {
                    $subQuery->where('nombre', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('apellido', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('correo', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('ci', 'LIKE', '%'.$buscar.'%');
                });
            })
            ->paginate(10);

        return view('conductor.index', compact('conductores', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $conductor = new Conductor;
        $usuarios = $this->usuariosDisponibles();

        return view('conductor.create', compact('conductor', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'estado' => 'required|in:activo,inactivo',
            'licencia' => ['nullable', 'string', 'max:30'],
            'user_id' => ['required', 'integer', 'exists:users,id', Rule::unique('conductor', 'user_id')],
        ]);

        Conductor::create($request->all());

        return redirect()->route('conductor.index')->with('success', 'Conductor creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $conductor = Conductor::findOrFail($id);

        return view('conductor.show', compact('conductor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $conductor = Conductor::findOrFail($id);
        $usuarios = $this->usuariosDisponibles($conductor);

        return view('conductor.edit', compact('conductor', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $conductor = Conductor::findOrFail($id);

        $request->validate([
            'estado' => 'required|in:activo,inactivo',
            'licencia' => ['nullable', 'string', 'max:30'],
            'user_id' => ['nullable', 'integer', 'exists:users,id', Rule::unique('conductor', 'user_id')->ignore($conductor->id)],
        ]);

        $this->integridad->asegurarCambioDeEstadoPermitido($conductor, $request->all());

        $conductor->update($request->all());

        return redirect()->route('conductor.index')->with('success', 'Conductor actualizado con éxito.');
    }

    /**
     * Logical delete: set estado to inactivo
     */
    public function destroy(string $id)
    {
        $conductor = Conductor::findOrFail($id);

        try {
            $this->integridad->desactivar($conductor);
        } catch (ValidationException $e) {
            return redirect()->route('conductor.index')
                ->with('error', $e->validator->errors()->first('estado'));
        }

        return redirect()->route('conductor.index')->with('success', 'Conductor eliminado con éxito.');
    }

    private function usuariosDisponibles(?Conductor $conductor = null)
    {
        return User::query()
            ->where(function ($query) use ($conductor) {
                $query->whereDoesntHave('conductor');

                if ($conductor?->user_id) {
                    $query->orWhereKey($conductor->user_id);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'apellido', 'email', 'telefono', 'ci']);
    }
}
