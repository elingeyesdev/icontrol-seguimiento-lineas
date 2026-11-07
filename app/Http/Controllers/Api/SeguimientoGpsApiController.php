<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SincronizarUbicacionesGpsRequest;
use App\Http\Requests\Api\StoreUbicacionGpsRequest;
use App\Models\AsignacionTurno;
use App\Services\SeguimientoGpsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeguimientoGpsApiController extends Controller
{
    public function __construct(
        protected SeguimientoGpsService $seguimientoGpsService
    ) {}

    /**
     * Ingesta de un único punto GPS en tiempo real.
     */
    public function guardarUbicacion(StoreUbicacionGpsRequest $request, int $asignacionId): JsonResponse
    {
        $asignacion = AsignacionTurno::noCanceladas()->findOrFail($asignacionId);
        $conductor = $request->user()->conductor;

        if (! $conductor || (int) $asignacion->conductor_id !== (int) $conductor->id) {
            return response()->json([
                'message' => 'No tienes autorización para reportar GPS en esta asignación.',
            ], 403);
        }

        $resultado = $this->seguimientoGpsService->registrarPunto($asignacion, $request->validated());

        return response()->json([
            'message' => 'Ubicación procesada correctamente.',
            'data' => $resultado,
        ], 201);
    }

    /**
     * Sincronización en lote de ubicaciones almacenadas offline.
     */
    public function sincronizarLote(SincronizarUbicacionesGpsRequest $request): JsonResponse
    {
        $asignacion = AsignacionTurno::noCanceladas()->findOrFail($request->input('asignacion_turno_id'));
        $conductor = $request->user()->conductor;

        if (! $conductor || (int) $asignacion->conductor_id !== (int) $conductor->id) {
            return response()->json([
                'message' => 'No tienes autorización para reportar GPS en esta asignación.',
            ], 403);
        }

        $resultado = $this->seguimientoGpsService->registrarLote($asignacion, $request->input('ubicaciones'));

        return response()->json([
            'message' => 'Sincronización procesada.',
            'resultado' => $resultado,
        ]);
    }

    /**
     * Consulta el estado del recorrido y paradas cumplidas de una asignación.
     */
    public function estadoRecorrido(Request $request, int $asignacionId): JsonResponse
    {
        $asignacion = AsignacionTurno::with([
            'turno',
            'micro.interno',
            'ruta.paradas',
            'controlesRecorrido.rutaParada.parada',
            'seguimientosGps' => fn ($q) => $q->latest('fecha_hora_gps')->take(1),
        ])->noCanceladas()->findOrFail($asignacionId);
        $conductor = $request->user()->conductor;

        if (! $conductor || (int) $asignacion->conductor_id !== (int) $conductor->id) {
            return response()->json([
                'message' => 'No tienes autorización para consultar esta asignación.',
            ], 403);
        }

        return response()->json([
            'asignacion' => $asignacion,
            'ultima_posicion' => $asignacion->seguimientosGps->first(),
            'paradas_cumplidas' => $asignacion->controlesRecorrido->where('estado', 'cumplido')->values(),
        ]);
    }
}
