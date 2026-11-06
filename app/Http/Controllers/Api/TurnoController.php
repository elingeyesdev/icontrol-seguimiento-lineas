<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use App\Services\IntegridadAsignacionesService;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function __construct(protected IntegridadAsignacionesService $integridad) {}

    public function index()
    {
        return response()->json(Turno::where('estado', '!=', 'inactivo')->get());
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $turno = Turno::create($data);

        return response()->json($turno, 201);
    }

    public function show(string $id)
    {
        return response()->json(Turno::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $turno = Turno::findOrFail($id);
        $datos = $this->validar($request, true);
        $this->integridad->asegurarCambioDeEstadoPermitido($turno, $datos);
        $turno->update($datos);

        return response()->json($turno->fresh());
    }

    public function destroy(string $id)
    {
        $turno = Turno::findOrFail($id);
        $this->integridad->desactivar($turno);

        return response()->json(['message' => 'Turno desactivado correctamente']);
    }

    /**
     * El cliente móvil envía `tipo` y `fiscalizador_id`, columnas que se
     * eliminaron en la migración 2026_08_13_000001. Se acepta `tipo` como
     * alias de `nombre` y se descarta `fiscalizador_id` para no romperlo.
     */
    protected function validar(Request $request, bool $partial = false): array
    {
        $requerido = $partial ? 'sometimes' : 'required';

        // La app móvil envía "Mañana" y horas en formato HH:MM. El catálogo
        // usa el enum mañana/tarde/noche y columnas TIME (HH:MM:SS).
        $nombre = $request->input('nombre', $request->input('tipo'));
        if ($nombre !== null) {
            $request->merge(['nombre' => $this->normalizarNombre((string) $nombre)]);
        }

        foreach (['hora_inicio', 'hora_fin'] as $campo) {
            if ($request->filled($campo)) {
                $request->merge([$campo => $this->normalizarHora((string) $request->input($campo))]);
            }
        }

        $datos = $request->validate([
            'nombre' => [$requerido, 'string', 'in:mañana,tarde,noche'],
            'tipo' => ['nullable', 'string', 'in:mañana,tarde,noche'],
            'hora_inicio' => [$requerido, 'date_format:H:i:s'],
            'hora_fin' => [$requerido, 'date_format:H:i:s'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['nullable', 'string', 'in:activo,inactivo'],
            'fiscalizador_id' => ['nullable', 'integer'],
        ]);

        $datos['nombre'] = $datos['nombre'] ?? $datos['tipo'] ?? null;
        unset($datos['tipo'], $datos['fiscalizador_id']);

        return $datos;
    }

    private function normalizarNombre(string $nombre): string
    {
        $clave = mb_strtolower(trim($nombre));

        return [
            'manana' => 'mañana',
            'mañana' => 'mañana',
            'tarde' => 'tarde',
            'noche' => 'noche',
        ][$clave] ?? $nombre;
    }

    private function normalizarHora(string $hora): string
    {
        $partes = array_map('trim', explode(':', $hora));

        if (count($partes) === 2) {
            $partes[] = '00';
        }

        if (count($partes) !== 3 || ! is_numeric($partes[0]) || ! is_numeric($partes[1]) || ! is_numeric($partes[2])) {
            return $hora;
        }

        return sprintf('%02d:%02d:%02d', (int) $partes[0], (int) $partes[1], (int) $partes[2]);
    }
}
