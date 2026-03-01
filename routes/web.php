<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;

// ========== Rutas de autenticación (invitados) ==========
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ========== Rutas del panel (requieren autenticación) ==========
Route::middleware('auth')->group(function () {
    // Dashboard (inicio migrado)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Páginas en desarrollo (mensaje único)
    Route::get('/en-desarrollo', function () {
        return view('pages.en-desarrollo', ['title' => 'En desarrollo']);
    })->name('en-desarrollo');

    // Calendario
    Route::get('/calendar', function () {
        return view('pages.calender', ['title' => 'Calendario']);
    })->name('calendar');

    // Perfil
    Route::get('/profile', function () {
        return view('pages.profile', ['title' => 'Perfil']);
    })->name('profile');

    // Formularios
    Route::get('/form-elements', function () {
        return view('pages.form.form-elements', ['title' => 'Elementos de formulario']);
    })->name('form-elements');

    // Tablas
    Route::get('/basic-tables', function () {
        return view('pages.tables.basic-tables', ['title' => 'Tablas básicas']);
    })->name('basic-tables');

    Route::get('/blank', function () {
        return view('pages.blank', ['title' => 'Blank']);
    })->name('blank');

    Route::get('/error-404', function () {
        return view('pages.errors.error-404', ['title' => 'Error 404']);
    })->name('error-404');

    // Gráficos
    Route::get('/line-chart', function () {
        return view('pages.chart.line-chart', ['title' => 'Gráfico lineal']);
    })->name('line-chart');

    Route::get('/bar-chart', function () {
        return view('pages.chart.bar-chart', ['title' => 'Gráfico de barras']);
    })->name('bar-chart');

    // UI
    Route::get('/alerts', function () {
        return view('pages.ui-elements.alerts', ['title' => 'Alertas']);
    })->name('alerts');

    Route::get('/avatars', function () {
        return view('pages.ui-elements.avatars', ['title' => 'Avatares']);
    })->name('avatars');

    Route::get('/badge', function () {
        return view('pages.ui-elements.badges', ['title' => 'Badges']);
    })->name('badges');

    Route::get('/buttons', function () {
        return view('pages.ui-elements.buttons', ['title' => 'Botones']);
    })->name('buttons');

    Route::get('/image', function () {
        return view('pages.ui-elements.images', ['title' => 'Imágenes']);
    })->name('images');

    Route::get('/videos', function () {
        return view('pages.ui-elements.videos', ['title' => 'Videos']);
    })->name('videos');

    // Sign in/up (solo para usuarios ya logueados que accedan por URL)
    Route::get('/signin', fn () => redirect()->route('dashboard'));
    Route::get('/signup', fn () => redirect()->route('dashboard'));
});
