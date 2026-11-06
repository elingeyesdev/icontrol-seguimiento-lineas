<?php

namespace App\Http\Controllers;

use App\Models\Parada;
use App\Models\Ruta;
use App\Services\IntegridadAsignacionesService;
use App\Services\RutaParametrizacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RutaController extends Controller
{
    public function __construct(
        protected RutaParametrizacionService $parametrizacionService,
        protected IntegridadAsignacionesService $integridad,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $buscar = $request->input('buscar');
        $rutas = Ruta::withCount('paradas')
            ->where('estado', '!=', 'inactivo')
            ->when($buscar, function ($query, $buscar) {
                return $query->where(function ($subQuery) use ($buscar) {
                    $subQuery->where('nombre', 'LIKE', "%{$buscar}%")
                        ->orWhere('descripcion', 'LIKE', "%{$buscar}%");
                });
            })
            ->paginate(10);

        return view('ruta.index', compact('rutas', 'buscar'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $ruta = new Ruta;
        $paradas = Parada::where('estado', 'activo')->orderBy('nombre')->get();
        $paradasIda = collect();
        $paradasVuelta = collect();

        return view('ruta.create', compact('ruta', 'paradas', 'paradasIda', 'paradasVuelta'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ruta', 'nombre'),
            ],
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'paradas_ida' => 'nullable|array',
            'paradas_ida.*' => 'exists:paradas,id',
            'paradas_vuelta' => 'nullable|array',
            'paradas_vuelta.*' => 'exists:paradas,id',
        ], [
            'nombre.unique' => 'Ya existe una ruta registrada con este nombre.',
        ]);

        $ruta = Ruta::create($request->only(['nombre', 'descripcion', 'estado']));

        $this->parametrizacionService->guardarParametrizacion(
            $ruta,
            $request->input('paradas_ida', []),
            $request->input('paradas_vuelta', [])
        );

        return Redirect::route('ruta.index')
            ->with('success', 'Ruta creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $ruta = Ruta::with(['paradasIda', 'paradasVuelta'])->findOrFail($id);

        return view('ruta.show', compact('ruta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $ruta = Ruta::findOrFail($id);
        $paradas = Parada::where('estado', 'activo')->orderBy('nombre')->get();
        $paradasIda = $ruta->paradasIda()->get();
        $paradasVuelta = $ruta->paradasVuelta()->get();

        return view('ruta.edit', compact('ruta', 'paradas', 'paradasIda', 'paradasVuelta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $ruta = Ruta::findOrFail($id);

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:50',
                Rule::unique('ruta', 'nombre')->ignore($ruta->id),
            ],
            'descripcion' => 'nullable|string',
            'estado' => 'required|in:activo,inactivo',
            'paradas_ida' => 'nullable|array',
            'paradas_ida.*' => 'exists:paradas,id',
            'paradas_vuelta' => 'nullable|array',
            'paradas_vuelta.*' => 'exists:paradas,id',
        ], [
            'nombre.unique' => 'Ya existe otra ruta registrada con este nombre.',
        ]);

        $datos = $request->only(['nombre', 'descripcion', 'estado']);
        $this->integridad->asegurarCambioDeEstadoPermitido($ruta, $datos);

        $ruta->update($datos);

        $this->parametrizacionService->guardarParametrizacion(
            $ruta,
            $request->input('paradas_ida', []),
            $request->input('paradas_vuelta', [])
        );

        return Redirect::route('ruta.index')
            ->with('success', 'Ruta actualizada exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        $ruta = Ruta::findOrFail($id);

        try {
            $this->integridad->desactivar($ruta);
        } catch (ValidationException $e) {
            return Redirect::route('ruta.index')
                ->with('error', $e->validator->errors()->first('estado'));
        }

        return Redirect::route('ruta.index')
            ->with('success', 'Ruta desactivada exitosamente.');
    }
}
