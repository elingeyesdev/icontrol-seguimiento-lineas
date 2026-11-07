<?php

use App\Http\Controllers\AsignacionTurnoController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\ControlRecorridoController;
use App\Http\Controllers\InternoController;
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

    return view('dashboard', compact('microsActivos', 'conductoresDisponibles', 'recorridosActivos', 'microsFueraServicio'));
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
