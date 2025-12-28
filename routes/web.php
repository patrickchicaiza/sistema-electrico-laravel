<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReporteController;

// Redirigir página principal a login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Auth::routes(['register' => true]); // Permitir registro

// Redirigir /home a dashboard
Route::get('/home', function () {
    return redirect()->route('dashboard');
});

// ===== TODAS LAS RUTAS PROTEGIDAS =====
Route::middleware(['auth'])->group(function () {

    // Dashboard según rol (sin middleware extra)
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Redirigir según rol
        if ($user->hasRole('cliente')) {
            return redirect()->route('reportes.index');
        } elseif ($user->hasRole('tecnico')) {
            return redirect()->route('reportes.index');
        } elseif ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            return view('dashboard.admin');
        }

        return view('dashboard');
    })->name('dashboard');

    // Rutas con permisos YA controlados en los controladores
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('reportes', ReporteController::class);

    // Rutas adicionales
    Route::post('/reportes/{reporte}/asignar', [ReporteController::class, 'asignar'])
        ->name('reportes.asignar');

    Route::post('/reportes/{reporte}/cambiar-estado', [ReporteController::class, 'cambiarEstado'])
        ->name('reportes.cambiar-estado');
});

// ===== CONFIGURAR LOGOUT PARA REDIRIGIR A LOGIN =====
// Esto ya debería estar en LoginController, pero por si acaso:
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');