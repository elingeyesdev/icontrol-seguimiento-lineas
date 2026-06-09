<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\ConductorApiController;
use App\Http\Controllers\Api\DueñoApiController;
use App\Http\Controllers\Api\MicroApiController;
use App\Http\Controllers\Api\RutaApiController;
use App\Http\Controllers\Api\InternoApiController;
use App\Http\Controllers\Api\ParadaApiController;
use App\Http\Controllers\Api\TurnoApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class,'login']);

// Rutas protegidas con autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('clientes', ClienteApiController::class);
    Route::apiResource('conductores', ConductorApiController::class);
    Route::apiResource('dueños', DueñoApiController::class);
    Route::apiResource('micros', MicroApiController::class);
    Route::apiResource('rutas', RutaApiController::class);
    Route::apiResource('internos', InternoApiController::class);
    Route::apiResource('paradas', ParadaApiController::class);
    Route::apiResource('turnos', TurnoApiController::class);
});

