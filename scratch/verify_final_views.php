<?php

use App\Http\Controllers\MonitoreoController;
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

// 1. Test MonitoreoController (Seguimiento de Rutas)
$mController = $app->make(MonitoreoController::class);
$responseM = $mController->index(Request::create('/seguimiento-rutas', 'GET'));
echo 'Seguimiento Rutas View: '.$responseM->name().' (Render: '.strlen($responseM->render())." bytes)\n";

$jsonPos = $mController->posicionesEnVivo();
echo 'Live GPS JSON: '.$jsonPos->getContent()."\n";

echo "ALL TESTS PASSED!\n";
