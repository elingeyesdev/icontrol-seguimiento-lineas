<?php

use App\Http\Controllers\AsignacionTurnoController;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::first();
Auth::login($user);

$controller = $app->make(AsignacionTurnoController::class);
$request = Request::create('/asignacion-turno', 'GET');
$response = $controller->index($request);

echo 'View name: '.$response->name()."\n";
$viewRendered = $response->render();
echo 'Rendered length: '.strlen($viewRendered)." bytes\n";
echo "SUCCESS!\n";
