<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Conductor;
class ConductorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $conductores = Conductor::all();

        return response()->json($conductores);
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

        $conductor = Conductor::create($request->all());

        return response()->json($conductor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $conductor = Conductor::findOrFail($id);

        return response()->json($conductor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
        $conductor = Conductor::findOrFail($id);
        $conductor->update($request->all());
        return response()->json($conductor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
          Conductor ::destroy($id);
        return response()->json([
            'message' => 'Conductor eliminado exitosamente'
        ]);
    }
}
