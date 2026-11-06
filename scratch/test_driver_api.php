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

$asignacion = AsignacionTurno::where('conductor_id', $conductor->id)
    ->where('fecha', now()->toDateString())
    ->whereIn('estado', ['pendiente', 'en_curso', 'retrasado'])
    ->with(['turno', 'micro.interno', 'ruta.paradas'])
    ->first();

echo 'User: '.$user->name.' '.$user->apellido."\n";
echo 'Conductor ID: '.$conductor->id.' | Licencia: '.$conductor->licencia."\n";
echo 'Asignacion ID: '.($asignacion->id ?? 'Ninguna')."\n";
echo 'Placa: '.($asignacion->micro->placa ?? 'N/A').' | Interno: '.($asignacion->micro->interno->numero_interno ?? 'N/A')."\n";
echo 'Ruta: '.($asignacion->ruta->nombre ?? 'N/A')."\n";
echo 'Turno: '.($asignacion->turno->nombre ?? 'N/A').' ('.($asignacion->turno->hora_inicio ?? '').' - '.($asignacion->turno->hora_fin ?? '').")\n";
echo 'Estado: '.($asignacion->estado ?? 'N/A')."\n";
