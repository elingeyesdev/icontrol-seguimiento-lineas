<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropietarioRequest;
use App\Models\Propietario;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PropietarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');
        $propietarios = Propietario::where('estado', '!=', 'inactivo')
            ->when($buscar, function ($query, $buscar) {
                return $query->where(function ($subQuery) use ($buscar) {
                    $subQuery->where('nombre', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('apellido', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('correo', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('ci', 'LIKE', '%'.$buscar.'%');
                });
            })
            ->paginate(10);

        return view('propietario.index', compact('propietarios', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $propietario = new Propietario;
        $usuarios = $this->usuariosDisponibles();

        return view('propietario.create', compact('propietario', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PropietarioRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['fecha_registro'])) {
            $data['fecha_registro'] = now()->toDateString();
        }
        Propietario::create($data);

        return Redirect::route('propietario.index')
            ->with('success', 'Propietario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $propietario = Propietario::findOrFail($id);

        return view('propietario.show', compact('propietario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $propietario = Propietario::findOrFail($id);
        $usuarios = $this->usuariosDisponibles($propietario);

        return view('propietario.edit', compact('propietario', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PropietarioRequest $request, $id): RedirectResponse
    {
        $propietario = Propietario::findOrFail($id);
        $propietario->update($request->validated());

        return Redirect::route('propietario.index')
            ->with('success', 'Propietario actualizado correctamente.');
    }

    /**
     * Logical delete: set estado to inactivo
     */
    public function destroy($id): RedirectResponse
    {
        $propietario = Propietario::findOrFail($id);
        $propietario->update(['estado' => 'inactivo']);

        return Redirect::route('propietario.index')
            ->with('success', 'Propietario desactivado correctamente.');
    }

    private function usuariosDisponibles(?Propietario $propietario = null)
    {
        return User::query()
            ->where(function ($query) use ($propietario) {
                $query->whereDoesntHave('propietario');

                if ($propietario?->user_id) {
                    $query->orWhereKey($propietario->user_id);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'apellido', 'email', 'telefono', 'ci']);
    }
}
