<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interno;
use Illuminate\Http\Request;

class InternoApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Interno::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $interno = Interno::create($request->all());

        return response()->json($interno,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Interno::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $interno = Interno::findOrFail($id);
        $interno->update($request->all());
        return response()->json($interno);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Interno::destroy($id);
        return response()->json([
            'message' => 'Interno eliminado exitosamente'
        ]);
    }
}
