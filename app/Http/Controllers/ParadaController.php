<?php

namespace App\Http\Controllers;

use App\Models\Parada;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ParadaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ParadaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $paradas = Parada::paginate();

        return view('parada.index', compact('paradas'))
            ->with('i', ($request->input('page', 1) - 1) * $paradas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $parada = new Parada();

        return view('parada.create', compact('parada'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ParadaRequest $request): RedirectResponse
    {
        Parada::create($request->validated());

        return Redirect::route('paradas.index')
            ->with('success', 'Parada created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $parada = Parada::find($id);

        return view('parada.show', compact('parada'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $parada = Parada::find($id);

        return view('parada.edit', compact('parada'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ParadaRequest $request, Parada $parada): RedirectResponse
    {
        $parada->update($request->validated());

        return Redirect::route('paradas.index')
            ->with('success', 'Parada updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Parada::find($id)->delete();

        return Redirect::route('paradas.index')
            ->with('success', 'Parada deleted successfully');
    }
}
