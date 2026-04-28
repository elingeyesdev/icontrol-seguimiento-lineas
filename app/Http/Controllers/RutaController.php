<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RutaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RutaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');
        $rutas = Ruta::where('origen', 'LIKE', '%' . $buscar . '%')
                     ->orWhere('destino', 'LIKE', '%' . $buscar . '%')
                     ->orWhere('nombre_ruta', 'LIKE', '%' . $buscar . '%')
                     ->paginate(10);

        return view('ruta.index', compact('rutas', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $ruta = new Ruta();

        return view('ruta.create', compact('ruta'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RutaRequest $request): RedirectResponse
    {
        Ruta::create($request->validated());

        return Redirect::route('ruta.index')
            ->with('success', 'Ruta created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $ruta = Ruta::find($id);

        return view('ruta.show', compact('ruta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $ruta = Ruta::find($id);

        return view('ruta.edit', compact('ruta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RutaRequest $request, Ruta $ruta): RedirectResponse
    {
        $ruta->update($request->validated());

        return Redirect::route('ruta.index')
            ->with('success', 'Ruta updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Ruta::find($id)->delete();

        return Redirect::route('ruta.index')
            ->with('success', 'Ruta deleted successfully');
    }
}
