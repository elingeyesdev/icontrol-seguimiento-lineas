<?php

namespace App\Http\Controllers;

use App\Models\Parada;
use App\Models\Ruta;
use App\Models\RutaParada;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RutaParadaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $rutasParadas = RutaParada::with(['ruta', 'parada'])
            ->when($buscar, function ($query) use ($buscar) {
                $query->whereHas('ruta', function ($q) use ($buscar) {
                    $q->where('nombre', 'like', "%{$buscar}%");
                })
                    ->orWhereHas('parada', function ($q) use ($buscar) {
                        $q->where('nombre', 'like', "%{$buscar}%");
                    });
            })
            ->paginate(10);

        return view('rutas_paradas.index', compact('rutasParadas', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $rutas = Ruta::where('estado', 'activo')->get();
        $paradas = Parada::where('estado', 'activo')->get();
        $rutaParada = new RutaParada;

        return view('rutas_paradas.create', compact('rutas', 'paradas', 'rutaParada'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruta_id' => [
                'required',
                'exists:ruta,id',
                Rule::unique('parada_ruta', 'ruta_id')->where(fn ($q) => $q->where('parada_id', $request->input('parada_id'))
                    ->where('sentido', $request->input('sentido'))
                ),
            ],
            'parada_id' => 'required|exists:paradas,id',
            'orden' => 'required|integer|min:1',
            'sentido' => 'required|in:Ida,Vuelta',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'ruta_id.unique' => 'Esta parada ya está asociada a la ruta en el mismo sentido.',
        ]);

        RutaParada::create($validated);

        return redirect()->route('rutas-paradas.index')
            ->with('success', 'Relación Ruta-Parada creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rutaParada = RutaParada::with(['ruta', 'parada'])->findOrFail($id);

        return view('rutas_paradas.show', compact('rutaParada'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rutaParada = RutaParada::findOrFail($id);
        $rutas = Ruta::all();
        $paradas = Parada::all();

        return view('rutas_paradas.edit', compact('rutaParada', 'rutas', 'paradas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rutaParada = RutaParada::findOrFail($id);

        $validated = $request->validate([
            'ruta_id' => [
                'required',
                'exists:ruta,id',
                Rule::unique('parada_ruta', 'ruta_id')
                    ->where(fn ($q) => $q->where('parada_id', $request->input('parada_id'))
                        ->where('sentido', $request->input('sentido'))
                    )
                    ->ignore($rutaParada->id),
            ],
            'parada_id' => 'required|exists:paradas,id',
            'orden' => 'required|integer|min:1',
            'sentido' => 'required|in:Ida,Vuelta',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'ruta_id.unique' => 'Esta parada ya está asociada a la ruta en el mismo sentido.',
        ]);

        $rutaParada->update($validated);

        return redirect()->route('rutas-paradas.index')
            ->with('success', 'Relación Ruta-Parada actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage (Logical delete).
     */
    public function destroy($id)
    {
        $rutaParada = RutaParada::findOrFail($id);
        $rutaParada->desactivar();

        return redirect()->route('rutas-paradas.index')
            ->with('success', 'Relación Ruta-Parada desactivada con éxito.');
    }
}
