<?php

namespace App\Http\Controllers;

use App\Models\Micro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\MicroRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MicroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');
        $micros = Micro::where('placa', 'LIKE', '%' . $buscar . '%')
                       ->orWhere('modelo', 'LIKE', '%' . $buscar . '%')
                       ->orWhere('marca', 'LIKE', '%' . $buscar . '%')
                       ->paginate(10);

        return view('micro.index', compact('micros', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $micro = new Micro();

        return view('micro.create', compact('micro'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MicroRequest $request): RedirectResponse
    {
        Micro::create($request->validated());

        return Redirect::route('micro.index')
            ->with('success', 'Micro created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $micro = Micro::find($id);

        return view('micro.show', compact('micro'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $micro = Micro::find($id);

        return view('micro.edit', compact('micro'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MicroRequest $request, Micro $micro): RedirectResponse
    {
        $micro->update($request->validated());

        return Redirect::route('micro.index')
            ->with('success', 'Micro updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Micro::find($id)->delete();

        return Redirect::route('micro.index')
            ->with('success', 'Micro deleted successfully');
    }
}
