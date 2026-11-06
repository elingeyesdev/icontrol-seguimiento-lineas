<?php

use App\Models\AsignacionTurno;
use App\Models\Parada;
use App\Models\SeguimientoGps;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$paradasSCZ = [
    1 => ['nombre' => 'Terminal Bimodal', 'referencia' => 'Av. Intermodal y Montes', 'latitud' => -17.79440000, 'longitud' => -63.16780000],
    2 => ['nombre' => 'Plaza 24 de Septiembre', 'referencia' => 'Centro Histórico', 'latitud' => -17.78330000, 'longitud' => -63.18210000],
    3 => ['nombre' => 'Parque Urbano', 'referencia' => '2do Anillo y Av. Argentina', 'latitud' => -17.78850000, 'longitud' => -63.17450000],
    4 => ['nombre' => 'Mercado Los Pozos', 'referencia' => 'Calle Suárez Arana', 'latitud' => -17.77800000, 'longitud' => -63.17700000],
    5 => ['nombre' => 'Hospital San Juan de Dios', 'referencia' => 'Calle Cuéllar y España', 'latitud' => -17.79150000, 'longitud' => -63.18700000],
    6 => ['nombre' => 'Campus UAGRM', 'referencia' => 'Av. Busch y 2do Anillo', 'latitud' => -17.77600000, 'longitud' => -63.19500000],
    7 => ['nombre' => 'Mercado Abasto', 'referencia' => '3er Anillo Interno y Av. Piraí', 'latitud' => -17.80500000, 'longitud' => -63.20100000],
];

foreach ($paradasSCZ as $id => $data) {
    $p = Parada::find($id);
    if ($p) {
        $p->update($data);
    }
}

// También actualizar la posición inicial GPS de la asignación 5 si existe
$asig = AsignacionTurno::find(5);
if ($asig) {
    SeguimientoGps::updateOrCreate(
        [
            'asignacion_turno_id' => $asig->id,
            'fecha_hora_gps' => now()->toDateTimeString(),
        ],
        [
            'latitud' => -17.78330000,
            'longitud' => -63.18210000,
            'velocidad' => 28.5,
            'fecha_hora_sincronizacion' => now(),
        ]
    );
}

echo "Paradas y GPS actualizadas a Santa Cruz de la Sierra!\n";
