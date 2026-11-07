<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parada;
use Illuminate\Http\Request;

class ParadaApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Parada::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'ubicacion' => 'required|string',
        ]);

        $parada = Parada::create($validated);
        return response()->json($parada, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Parada::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $parada = Parada::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'sometimes|string',
            'ubicacion' => 'sometimes|string',
        ]);

        $parada->update($validated);
        return response()->json($parada);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Parada::destroy($id);
        return response()->json(['message' => 'Parada eliminada exitosamente']);
    }
}
