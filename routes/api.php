<?php

use App\Http\Controllers\Api\AsignacionTurnoApi;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConductorController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\InternoController;
use App\Http\Controllers\Api\MicroController;
use App\Http\Controllers\Api\ParadaController;
use App\Http\Controllers\Api\PropietarioController;
use App\Http\Controllers\Api\RutaController;
use App\Http\Controllers\Api\SeguimientoGpsApiController;
use App\Http\Controllers\Api\TurnoController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Estado y reenvío disponibles mientras se completa la verificación.
    Route::get('/email/estado', [EmailVerificationController::class, 'estado']);
    Route::post('/email/reenviar', [EmailVerificationController::class, 'reenviar'])
        ->middleware('throttle:6,1');

    Route::middleware('verified')->group(function () {
        Route::middleware('role:admin|fiscalizador|propietario')->group(function () {
            Route::apiResource('conductores', ConductorController::class)->parameters(['conductores' => 'conductor']);
            Route::post('/conductores/{id}/vincular-usuario', [ConductorController::class, 'vincularUsuario']);
            Route::apiResource('propietarios', PropietarioController::class);
            Route::apiResource('internos', InternoController::class);
            Route::apiResource('micros', MicroController::class);
            Route::apiResource('rutas', RutaController::class);
            Route::apiResource('paradas', ParadaController::class);
            Route::apiResource('turnos', TurnoController::class);
            Route::apiResource('asignacion-turnos', AsignacionTurnoApi::class);
        });

        // Endpoints transaccionales móviles: rol y propiedad de asignación.
        Route::middleware('role:conductor')->group(function () {
            Route::get('/mis/asignaciones', [AsignacionTurnoApi::class, 'misAsignaciones']);
            Route::get('/mis/asignacion-actual', [AsignacionTurnoApi::class, 'miAsignacionActual']);
            Route::post('/mis/asignaciones/{id}/iniciar', [AsignacionTurnoApi::class, 'iniciar']);
            Route::post('/mis/asignaciones/{id}/finalizar', [AsignacionTurnoApi::class, 'finalizar']);

            // Ingesta y Seguimiento GPS con control de recorrido.
            Route::middleware('throttle:120,1')->group(function () {
                Route::post('/mis/asignaciones/{id}/ubicaciones', [SeguimientoGpsApiController::class, 'guardarUbicacion']);
                Route::post('/mis/ubicaciones/sincronizar', [SeguimientoGpsApiController::class, 'sincronizarLote']);
                Route::get('/mis/asignaciones/{id}/recorrido', [SeguimientoGpsApiController::class, 'estadoRecorrido']);
            });
        });
    });
});
