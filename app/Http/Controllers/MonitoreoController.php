<?php

namespace App\Http\Controllers;

use App\Services\SeguimientoGpsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MonitoreoController extends Controller
{
    public function __construct(
        protected SeguimientoGpsService $seguimientoGpsService
    ) {}

    /**
     * Vista principal de Seguimiento GPS en vivo.
     */
    public function index(): View
    {
        return view('monitoreo.index');
    }

    /**
     * Endpoint JSON para polling de posiciones de la flota (SDD Sección 16).
     */
    public function posicionesEnVivo(): JsonResponse
    {
        return response()->json($this->seguimientoGpsService->flotaEnVivo());
    }
}
