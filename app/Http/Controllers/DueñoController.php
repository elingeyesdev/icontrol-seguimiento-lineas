<?php

namespace App\Http\Controllers;

use App\Models\Dueño;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\DueñoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DueñoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');
        $dueños = Dueño::where('nombre', 'LIKE', '%' . $buscar . '%')
                       ->orWhere('apellido', 'LIKE', '%' . $buscar . '%')
                       ->orWhere('correo', 'LIKE', '%' . $buscar . '%')
                       ->orWhere('ci', 'LIKE', '%' . $buscar . '%')
                       ->paginate(10);

        return view('dueño.index', compact('dueños', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $dueño = new Dueño();

        return view('dueño.create', compact('dueño'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DueñoRequest $request): RedirectResponse
    {
        Dueño::create($request->validated());

        return Redirect::route('dueño.index')
            ->with('success', 'Dueño created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $dueño = Dueño::find($id);

        return view('dueño.show', compact('dueño'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $dueño = Dueño::find($id);

        return view('dueño.edit', compact('dueño'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DueñoRequest $request, Dueño $dueño): RedirectResponse
    {
        $dueño->update($request->validated());

        return Redirect::route('dueño.index')
            ->with('success', 'Dueño updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Dueño::find($id)->delete();

        return Redirect::route('dueño.index')
            ->with('success', 'Dueño deleted successfully');
    }
}
