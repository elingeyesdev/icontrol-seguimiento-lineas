<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Micro;
use Illuminate\Http\Request;

class MicroApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $micro = Micro::create($request->all());

        return response()->json($micro,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json(Micro::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $micro = Micro::findOrFail($id);
        $micro->update($request->all());
        return response()->json($micro);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Micro::destroy($id);
        return response()->json([
            'message' => 'Micro eliminado exitosamente'
        ]);
    }
}
