<?php

use App\Http\Controllers\AsignacionTurnoController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\ControlRecorridoController;
use App\Http\Controllers\InternoController;
use App\Http\Controllers\Kpis\CumplimientoParadasController;
use App\Http\Controllers\Kpis\TiempoPromedioRecorridoController;
use App\Http\Controllers\MicroController;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\ParadaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropietarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\RutaParadaController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\UserController;
use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Micro;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $microsActivos = Micro::where('estado', 'activo')->count();
    $conductoresDisponibles = Conductor::where('estado', 'activo')->count();
    $recorridosActivos = AsignacionTurno::where('fecha', now()->toDateString())->whereIn('estado', ['en_curso', 'pendiente', 'retrasado'])->count();
    $microsFueraServicio = Micro::where('estado', '!=', 'activo')->count();
    $usuario = request()->user();

    $tarjetas = [
        'Seguridad' => [
            ['titulo' => 'Usuarios', 'descripcion' => 'Administrar usuarios del sistema.', 'icono' => 'bi-people', 'ruta' => 'users.index', 'roles' => ['admin', 'propietario', 'fiscalizador']],
            ['titulo' => 'Roles', 'descripcion' => 'Gestionar roles y permisos.', 'icono' => 'bi-shield', 'ruta' => 'roles.index', 'roles' => ['admin', 'fiscalizador']],
            ['titulo' => 'Asignar roles', 'descripcion' => 'Asignar roles a los usuarios.', 'icono' => 'bi-person-gear', 'ruta' => 'roles.assign', 'roles' => ['admin', 'fiscalizador']],
        ],
        'Parametrización' => [
            ['titulo' => 'Propietarios', 'descripcion' => 'Gestionar propietarios de micros.', 'icono' => 'bi-person-vcard', 'ruta' => 'propietario.index'],
            ['titulo' => 'Internos', 'descripcion' => 'Gestionar números internos.', 'icono' => 'bi-hdd-stack', 'ruta' => 'interno.index'],
            ['titulo' => 'Micros', 'descripcion' => 'Gestionar la flota registrada.', 'icono' => 'bi-bus-front', 'ruta' => 'micro.index'],
            ['titulo' => 'Conductores', 'descripcion' => 'Gestionar conductores.', 'icono' => 'bi-person-badge', 'ruta' => 'conductor.index'],
            ['titulo' => 'Rutas', 'descripcion' => 'Gestionar rutas operativas.', 'icono' => 'bi-signpost-2', 'ruta' => 'ruta.index'],
            ['titulo' => 'Paradas', 'descripcion' => 'Gestionar paradas de las rutas.', 'icono' => 'bi-geo-alt', 'ruta' => 'parada.index'],
            ['titulo' => 'Turnos', 'descripcion' => 'Gestionar horarios y turnos.', 'icono' => 'bi-clock', 'ruta' => 'turno.index'],
        ],
        'Transacciones' => [
            ['titulo' => 'Asignación de turnos', 'descripcion' => 'Asignar conductores y micros.', 'icono' => 'bi-calendar2-check', 'ruta' => 'asignacion-turno.index'],
            ['titulo' => 'Control de recorrido', 'descripcion' => 'Supervisar el cumplimiento del recorrido.', 'icono' => 'bi-signpost-split', 'ruta' => 'control-recorrido.index'],
            ['titulo' => 'Seguimiento GPS', 'descripcion' => 'Consultar la ubicación de las unidades.', 'icono' => 'bi-geo-alt', 'ruta' => 'monitoreo.index'],
        ],
        'Reportes' => [
            ['titulo' => 'Asignaciones por conductor', 'descripcion' => 'Consultar asignaciones agrupadas.', 'icono' => 'bi-file-earmark-person', 'ruta' => 'reportes.show', 'parametros' => ['reporte' => 'asignaciones-conductor']],
            ['titulo' => 'Micros por propietario', 'descripcion' => 'Consultar micros registrados por propietario.', 'icono' => 'bi-file-earmark-spreadsheet', 'ruta' => 'reportes.show', 'parametros' => ['reporte' => 'micros-propietario']],
            ['titulo' => 'Paradas omitidas', 'descripcion' => 'Consultar paradas no cumplidas.', 'icono' => 'bi-file-earmark-bar-graph', 'ruta' => 'reportes.show', 'parametros' => ['reporte' => 'paradas-omitidas']],
        ],
        'Estadísticas y KPIs' => [
            ['titulo' => 'Tiempo promedio de recorrido', 'descripcion' => 'Analizar la duración de recorridos válidos.', 'icono' => 'bi-stopwatch', 'ruta' => 'kpis.tiempo-promedio-recorrido.index'],
            ['titulo' => 'Cumplimiento de paradas', 'descripcion' => 'Analizar el cumplimiento de paradas.', 'icono' => 'bi-check2-circle', 'ruta' => 'kpis.cumplimiento-paradas.index'],
        ],
    ];

    foreach ($tarjetas as $modulo => $items) {
        $tarjetas[$modulo] = array_values(array_filter($items, function (array $tarjeta) use ($usuario) {
            return ! isset($tarjeta['roles']) || $usuario->hasRole($tarjeta['roles']);
        }));
    }

    return view('dashboard', compact('microsActivos', 'conductoresDisponibles', 'recorridosActivos', 'microsFueraServicio', 'tarjetas', 'usuario'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pending-role', function () {
        return view('auth.pending-role');
    })->name('pending.role');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('ensure.role.assigned')->group(function () {
        Route::middleware('role:admin|fiscalizador|propietario')->group(function () {
            Route::get('seguimiento-rutas', [MonitoreoController::class, 'index'])->name('seguimiento-rutas.index');
            Route::get('monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');
            Route::get('monitoreo/posiciones', [MonitoreoController::class, 'posicionesEnVivo'])->name('monitoreo.posiciones');
            Route::get('control-recorrido', [ControlRecorridoController::class, 'index'])->name('control-recorrido.index');
            Route::get('control-recorrido/{id}', [ControlRecorridoController::class, 'show'])->name('control-recorrido.show');
            Route::resource('asignacion-turno', AsignacionTurnoController::class);
        });

        Route::middleware('role:admin|propietario|fiscalizador')->prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/', [ReporteController::class, 'index'])->name('index');
            Route::get('{reporte}/pdf', [ReporteController::class, 'pdf'])->name('pdf');
            Route::get('{reporte}/excel', [ReporteController::class, 'excel'])->name('excel');
            Route::get('{reporte}', [ReporteController::class, 'show'])->name('show');
        });

        Route::middleware('role:admin|propietario|fiscalizador')->group(function () {
            Route::prefix('kpis')->name('kpis.')->group(function () {
                Route::get('/tiempo-promedio-recorrido', [TiempoPromedioRecorridoController::class, 'index'])
                    ->name('tiempo-promedio-recorrido.index');
                Route::get('/cumplimiento-paradas', [CumplimientoParadasController::class, 'index'])
                    ->name('cumplimiento-paradas.index');
            });
            Route::resource('conductor', ConductorController::class);
            Route::resource('propietario', PropietarioController::class);
            Route::resource('micro', MicroController::class);
            Route::resource('interno', InternoController::class);
            Route::resource('ruta', RutaController::class);
            Route::resource('parada', ParadaController::class);
            Route::resource('turno', TurnoController::class);
            Route::resource('rutas-paradas', RutaParadaController::class);
        });

        Route::middleware('role:admin|fiscalizador')->group(function () {
            Route::get('roles/asignar', [RolesController::class, 'assignUsers'])->name('roles.assign');
            Route::get('roles/asignar/registrar', [RolesController::class, 'createUserRoleAssignment'])->name('roles.assign.create');
            Route::get('roles/asignar/{id}/editar', [RolesController::class, 'editUserRoleAssignment'])->name('roles.assign.edit');
            Route::post('roles/asignar', [RolesController::class, 'storeUserRoles'])->name('roles.assign.store');
            Route::delete('roles/asignar/{id}', [RolesController::class, 'destroyUserRoles'])->name('roles.assign.destroy');
            Route::resource('roles', RolesController::class);
        });

        Route::middleware('role:admin|propietario|fiscalizador')->group(function () {
            Route::resource('users', UserController::class);
        });
    });
});

require __DIR__.'/auth.php';
