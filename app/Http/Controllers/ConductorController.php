<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use Illuminate\Http\Request;

class ConductorController extends Controller
{
    public function __construct()
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
        $conductores = Conductor::where('nombre', 'LIKE', '%' . $buscar . '%')
                                ->orWhere('apellido', 'LIKE', '%' . $buscar . '%')
                                ->orWhere('correo', 'LIKE', '%' . $buscar . '%')
                                ->paginate(10);

        return view('conductor.index', compact('conductores', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('conductor.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:30',
            'apellido' => 'required|string|max:30',
            'telefono' => 'required|string|max:15',
            'correo' => 'required|email|unique:conductor,correo|max:50',
            'ci' => 'required|string|unique:conductor,ci|max:20',
        ]);

        Conductor::create($request->all());

        return redirect()->route('conductor.index')->with('info', 'Conductor creado con éxito');
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
        return view('conductor.edit', compact('conductor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $conductor = Conductor::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:30',
            'apellido' => 'required|string|max:30',
            'telefono' => 'required|string|max:15',
            'correo' => 'required|email|unique:conductor,correo,' . $id . '|max:50',
            'ci' => 'required|string|unique:conductor,ci,' . $id . '|max:20',
        ]);

        $conductor->update($request->all());

        return redirect()->route('conductor.index')->with('info', 'Conductor actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $conductor = Conductor::findOrFail($id);
        $conductor->delete();

        return redirect()->route('conductor.index')->with('info', 'Conductor eliminado con éxito');
    }
}
