<?php

use App\Models\AsignacionTurno;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'palvito23@gmail.com')->first();
$conductor = $user->conductor;

$asignaciones = AsignacionTurno::where('conductor_id', $conductor->id)
    ->with(['turno', 'micro.interno', 'ruta.paradas', 'conductor'])
    ->orderByDesc('fecha')
    ->orderByDesc('id')
    ->get();

$payload = [
    'conductor' => [
        'id' => $conductor->id,
        'licencia' => $conductor->licencia,
        'nombre_completo' => "{$user->name} {$user->apellido}",
    ],
    'total' => $asignaciones->count(),
    'asignaciones' => $asignaciones,
];

echo json_encode($payload, JSON_PRETTY_PRINT);
