<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\DueñoController;
use App\Http\Controllers\InternoController;
use App\Http\Controllers\MicroController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\RolesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('conductor', ConductorController::class);
    Route::resource('dueño', DueñoController::class);
    Route::resource('micro', MicroController::class);
    Route::resource('interno', InternoController::class);
    Route::resource('ruta', RutaController::class);
    Route::resource('turno', TurnoController::class);
    Route::resource('roles', RolesController::class);
    Route::resource('users', App\Http\Controllers\UserController::class);
});

require __DIR__.'/auth.php';
