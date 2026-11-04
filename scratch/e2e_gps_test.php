<?php

/**
 * Prueba E2E del envío de ubicaciones GPS desde la app móvil hasta el panel web.
 *
 * Se usan DOS asignaciones temporales para probar cada backend por separado:
 *   1. App -> ApiDart (:8000): ¿guarda el punto y compara la parada?
 *   2. Panel web (Laravel, misma BD): ¿ve la última posición?
 *   3. App -> Laravel (:8001): ¿guarda el punto y compara la parada?
 *   4. Panel web: ¿ve la última posición guardada por Laravel?
 *   5. Borra todos los datos temporales.
 *
 * Uso (con ApiDart en :8000 y Laravel en :8001):
 *   php scratch/e2e_gps_test.php
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\ControlRecorrido;
use App\Models\Micro;
use App\Models\Ruta;
use App\Models\RutaParada;
use App\Models\SeguimientoGps;
use App\Models\Turno;
use App\Models\User;
use App\Services\ControlRecorridoService;
use App\Services\SeguimientoGpsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

$laravelBase = 'http://127.0.0.1:8001';
$dartBase = 'http://127.0.0.1:8000';

$creados = [
    'user' => null,
    'conductor' => null,
    'asignaciones' => [],
];

function linea(string $texto = ''): void
{
    echo $texto.PHP_EOL;
}

function ok(bool $condicion): string
{
    return $condicion ? 'OK' : 'FALLO';
}

try {
    // ─────────────────────────────────────────────────────────────
    // 1. Datos temporales (dos asignaciones: una por backend)
    // ─────────────────────────────────────────────────────────────
    $user = User::factory()->create([
        'name' => 'E2E',
        'apellido' => 'GPS',
        'email' => 'e2e.gps.'.Str::lower(Str::random(8)).'@linea61.test',
    ]);
    $creados['user'] = $user;

    $conductor = Conductor::create([
        'user_id' => $user->id,
        'licencia' => 'E2E-'.Str::upper(Str::random(6)),
        'estado' => 'activo',
    ]);
    $creados['conductor'] = $conductor;

    $ruta = Ruta::find(1);
    $turnoId = Turno::value('id');
    $microId = Micro::value('id');

    if (! $ruta || ! $turnoId || ! $microId) {
        throw new RuntimeException('Faltan datos base (ruta 1 / turno / micro).');
    }

    $crearAsignacion = function () use (&$creados, $conductor, $ruta, $turnoId, $microId) {
        $asignacion = AsignacionTurno::create([
            'fecha' => now()->toDateString(),
            'turno_id' => $turnoId,
            'ruta_id' => $ruta->id,
            'micro_id' => $microId,
            'conductor_id' => $conductor->id,
            'estado' => 'en_curso',
            'hora_salida' => now()->format('H:i:s'),
        ]);
        app(ControlRecorridoService::class)->planificar($asignacion->fresh());
        $creados['asignaciones'][] = $asignacion;

        return $asignacion;
    };

    $asignacionDart = $crearAsignacion();
    $asignacionLaravel = $crearAsignacion();

    $rutaParada = RutaParada::where('ruta_id', $ruta->id)
        ->where('sentido', 'Ida')
        ->orderBy('orden')
        ->with('parada')
        ->firstOrFail();

    // Punto a ~4 metros de la primera parada (dentro del radio de 80 m).
    $lat = (float) $rutaParada->parada->latitud + 0.00003;
    $lon = (float) $rutaParada->parada->longitud + 0.00003;

    $token = $user->createToken('e2e-gps')->plainTextToken;

    $controlParada = fn (AsignacionTurno $asignacion) => ControlRecorrido::where('asignacion_turno_id', $asignacion->id)
        ->where('ruta_parada_id', $rutaParada->id)
        ->first();

    linea('=== PRUEBA E2E DE UBICACIONES GPS ===');
    linea("Conductor temporal #{$conductor->id} | ruta #{$ruta->id}");
    linea("Asignación ApiDart #{$asignacionDart->id} | Asignación Laravel #{$asignacionLaravel->id}");
    linea("Parada objetivo: {$rutaParada->parada->nombre} ({$lat}, {$lon})");
    linea();

    $payload = [
        'fecha_hora_gps' => now()->toIso8601String(),
        'latitud' => $lat,
        'longitud' => $lon,
        'velocidad' => 25.5,
    ];

    // ─────────────────────────────────────────────────────────────
    // 2. ApiDart: guardar + comparar
    // ─────────────────────────────────────────────────────────────
    $r1 = Http::timeout(15)->acceptJson()
        ->post("{$dartBase}/api/mis/asignaciones/{$asignacionDart->id}/ubicaciones", $payload);

    linea('[1] APP -> APIDART (backend configurado hoy en la app)');
    linea('    HTTP '.$r1->status().' | '.Str::limit($r1->body(), 220));

    $gpsDart = $asignacionDart->seguimientosGps()->count();
    $controlDart = $controlParada($asignacionDart);

    linea("    Puntos GPS guardados en la BD: {$gpsDart}");
    linea('    Control de la parada objetivo: '.($controlDart?->estado ?? 'sin control')
        .' | distancia: '.($controlDart?->distancia_metros ?? '—').' m');
    linea('    ¿ApiDart comparó la parada? '.ok($controlDart?->estado === 'cumplido'));
    linea();

    // ─────────────────────────────────────────────────────────────
    // 3. Panel web: ¿ve la posición guardada por ApiDart?
    // ─────────────────────────────────────────────────────────────
    $flota = app(SeguimientoGpsService::class)->flotaEnVivo();
    $unidadDart = collect($flota['unidades'])->firstWhere('asignacion_id', $asignacionDart->id);

    linea('[2] PANEL WEB (SeguimientoGpsService::flotaEnVivo)');
    linea($unidadDart
        ? "    Unidad visible: placa={$unidadDart['placa']} | lat={$unidadDart['latitud']} | lon={$unidadDart['longitud']} | hora={$unidadDart['ultima_actualizacion']}"
        : '    La unidad NO aparece en la flota en vivo');
    linea('    ¿La web recibe la ubicación? '.ok((bool) $unidadDart && $unidadDart['latitud'] !== null));
    linea();

    // ─────────────────────────────────────────────────────────────
    // 4. Laravel: guardar + comparar (asignación independiente)
    // ─────────────────────────────────────────────────────────────
    $payload2 = $payload;
    $payload2['fecha_hora_gps'] = now()->addSecond()->toIso8601String();

    $r2 = Http::timeout(15)->withToken($token)->acceptJson()
        ->post("{$laravelBase}/api/mis/asignaciones/{$asignacionLaravel->id}/ubicaciones", $payload2);

    linea('[3] APP -> LARAVEL (endpoint que compara paradas)');
    linea('    HTTP '.$r2->status().' | '.Str::limit($r2->body(), 320));

    $controlLaravel = $controlParada($asignacionLaravel);

    linea('    Control de la parada objetivo: '.($controlLaravel?->estado ?? 'sin control')
        .' | distancia: '.($controlLaravel?->distancia_metros ?? '—').' m');
    linea('    ¿Laravel comparó y marcó la parada? '.ok($controlLaravel?->estado === 'cumplido'));
    linea();

    // ─────────────────────────────────────────────────────────────
    // 5. Panel web: ¿ve la posición guardada por Laravel?
    // ─────────────────────────────────────────────────────────────
    $flota2 = app(SeguimientoGpsService::class)->flotaEnVivo();
    $unidadLaravel = collect($flota2['unidades'])->firstWhere('asignacion_id', $asignacionLaravel->id);

    linea('[4] PANEL WEB DESPUÉS DE GUARDAR EN LARAVEL');
    linea($unidadLaravel
        ? "    Unidad visible: placa={$unidadLaravel['placa']} | lat={$unidadLaravel['latitud']} | lon={$unidadLaravel['longitud']} | hora={$unidadLaravel['ultima_actualizacion']}"
        : '    La unidad NO aparece en la flota en vivo');
    linea('    ¿La web muestra la posición? '.ok((bool) $unidadLaravel && $unidadLaravel['latitud'] !== null));
    linea();

    // ─────────────────────────────────────────────────────────────
    // 5b. ApiDart: recorrido completo y cierre automático
    // ─────────────────────────────────────────────────────────────
    $asignacionCierre = $crearAsignacion();
    $paradasSecuencia = RutaParada::where('ruta_id', $ruta->id)
        ->where('estado', 'activo')
        ->orderBy('sentido')
        ->orderBy('orden')
        ->with('parada')
        ->get();

    foreach ($paradasSecuencia as $indice => $rp) {
        Http::timeout(15)->acceptJson()->post(
            "{$dartBase}/api/mis/asignaciones/{$asignacionCierre->id}/ubicaciones",
            [
                'fecha_hora_gps' => now()->addSeconds($indice)->toIso8601String(),
                'latitud' => (float) $rp->parada->latitud + 0.00001,
                'longitud' => (float) $rp->parada->longitud + 0.00001,
                'velocidad' => 20.0,
            ]
        );
    }

    $asignacionCierre->refresh();
    $cumplidas = ControlRecorrido::where('asignacion_turno_id', $asignacionCierre->id)
        ->where('estado', 'cumplido')
        ->count();

    linea('[5] APIDART: RECORRIDO COMPLETO');
    linea("    Paradas cumplidas: {$cumplidas} / {$paradasSecuencia->count()}");
    linea('    Estado final de la asignación: '.$asignacionCierre->estado);
    linea('    ¿Cierra el turno en la última parada? '.ok($asignacionCierre->estado === 'completado'));
    linea();

    // ─────────────────────────────────────────────────────────────
    // 6. Conclusión
    // ─────────────────────────────────────────────────────────────
    linea('=== CONCLUSIÓN ===');
    linea('App envía a ApiDart: HTTP '.$r1->status().' ('.ok($r1->successful()).')');
    linea('Web ve la última posición: '.ok((bool) $unidadDart && $unidadDart['latitud'] !== null));
    linea('ApiDart compara paradas: '.ok($controlDart?->estado === 'cumplido'));
    linea('Laravel compara paradas: '.ok($controlLaravel?->estado === 'cumplido'));
    linea('ApiDart cierra turno en la última parada: '.ok($asignacionCierre->estado === 'completado'));
} catch (Throwable $e) {
    linea('ERROR: '.$e->getMessage());
    linea($e->getFile().':'.$e->getLine());
    $codigoSalida = 1;
} finally {
    // ─────────────────────────────────────────────────────────────
    // 7. Limpieza de datos temporales
    // ─────────────────────────────────────────────────────────────
    try {
        foreach ($creados['asignaciones'] as $asignacion) {
            $idsControl = ControlRecorrido::where('asignacion_turno_id', $asignacion->id)->pluck('id');
            SeguimientoGps::whereIn('control_recorrido_id', $idsControl)->delete();
            ControlRecorrido::whereIn('id', $idsControl)->delete();
            $asignacion->delete();
        }

        if ($creados['user']) {
            $creados['user']->tokens()->delete();
        }

        if ($creados['conductor']) {
            $creados['conductor']->delete();
        }

        if ($creados['user']) {
            $creados['user']->delete();
        }

        linea();
        linea('Datos temporales eliminados.');
    } catch (Throwable $e) {
        linea('ADVERTENCIA: no se pudieron limpiar todos los datos: '.$e->getMessage());
    }
}

exit($codigoSalida ?? 0);
