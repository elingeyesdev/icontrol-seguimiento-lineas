<?php

namespace App\Http\Controllers;

use App\Http\Requests\InternoRequest;
use App\Models\Interno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class InternoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');
        $internos = Interno::where('estado', '!=', 'inactivo')
            ->when($buscar, function ($query, $buscar) {
                return $query->where(function ($subQuery) use ($buscar) {
                    $subQuery->where('numero_interno', 'LIKE', '%'.$buscar.'%')
                        ->orWhere('estado', 'LIKE', '%'.$buscar.'%');
                });
            })
            ->paginate(10);

        return view('interno.index', compact('internos', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $interno = new Interno;

        return view('interno.create', compact('interno'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InternoRequest $request): RedirectResponse
    {
        Interno::create($request->validated());

        return Redirect::route('interno.index')
            ->with('success', 'Interno creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $interno = Interno::findOrFail($id);

        return view('interno.show', compact('interno'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $interno = Interno::findOrFail($id);

        return view('interno.edit', compact('interno'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InternoRequest $request, $id): RedirectResponse
    {
        $interno = Interno::findOrFail($id);
        $interno->update($request->validated());

        return Redirect::route('interno.index')
            ->with('success', 'Interno actualizado correctamente.');
    }

    /**
     * Logical delete: set estado to inactivo
     */
    public function destroy($id): RedirectResponse
    {
        $interno = Interno::findOrFail($id);
        $interno->update(['estado' => 'inactivo']);

        return Redirect::route('interno.index')
            ->with('success', 'Interno desactivado correctamente.');
    }
}
