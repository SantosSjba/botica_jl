<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Caja\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\Compras\ComprasController;
use App\Http\Controllers\Compras\ConsultaComprasController;
use App\Http\Controllers\ConsultaProductosController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Reportes\CuadreCajaController;
use App\Http\Controllers\Reportes\RptComprasController;
use App\Http\Controllers\Reportes\RptVentasController;
use App\Http\Controllers\Mantenimiento\CategoriaController as MantenimientoCategoriaController;
use App\Http\Controllers\Mantenimiento\LoteController as MantenimientoLoteController;
use App\Http\Controllers\Mantenimiento\PresentacionController as MantenimientoPresentacionController;
use App\Http\Controllers\Mantenimiento\ProductoController as MantenimientoProductoController;
use App\Http\Controllers\Mantenimiento\SintomaController as MantenimientoSintomaController;
use App\Http\Controllers\Mantenimiento\UsuarioController as MantenimientoUsuarioController;

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

    // Consultas
    Route::get('/consulta/productos', [ConsultaProductosController::class, 'index'])->name('consulta.productos');

    // Mantenimiento: Cliente / Laboratorio (accesible por ADMIN y USUARIO)
    Route::resource('mantenimiento/clientes', ClienteController::class)->names('mantenimiento.clientes');

    // Mantenimiento: Producto (solo ADMIN) — export antes del resource para no capturar por show
    Route::get('mantenimiento/productos/export/excel', [MantenimientoProductoController::class, 'exportExcel'])->name('mantenimiento.productos.export.excel');
    Route::get('mantenimiento/productos/export/pdf', [MantenimientoProductoController::class, 'exportPdf'])->name('mantenimiento.productos.export.pdf');
    Route::resource('mantenimiento/productos', MantenimientoProductoController::class)->names('mantenimiento.productos');

    // Caja: apertura, cierre, seguimiento (ambos roles; visibilidad por estado en menú)
    Route::get('/caja/apertura', [CajaController::class, 'apertura'])->name('caja.apertura');
    Route::post('/caja/apertura', [CajaController::class, 'storeApertura'])->name('caja.store-apertura');
    Route::get('/caja/cierre', [CajaController::class, 'cierre'])->name('caja.cierre');
    Route::post('/caja/cierre', [CajaController::class, 'storeCierre'])->name('caja.store-cierre');
    Route::get('/caja/seguimiento', [CajaController::class, 'seguimiento'])->name('caja.seguimiento');

    // Reportes: cuadre de caja (acceso según rol)
    Route::get('/reportes/cuadrecaja', [CuadreCajaController::class, 'show'])->name('reportes.cuadrecaja');

    // Reportes: ventas y compras (solo ADMINISTRADOR)
    Route::middleware('rol.administrador')->group(function () {
        Route::get('/reportes/ventas', [RptVentasController::class, 'ventasRango'])->name('reportes.ventas.rango');
        Route::get('/reportes/ventas-dia', [RptVentasController::class, 'ventasDia'])->name('reportes.ventas.dia');
        Route::get('/reportes/compras', [RptComprasController::class, 'comprasRango'])->name('reportes.compras.rango');
        Route::get('/reportes/compras-dia', [RptComprasController::class, 'comprasDia'])->name('reportes.compras.dia');
    });

    // Compras (solo ADMINISTRADOR)
    Route::middleware('rol.administrador')->group(function () {
        Route::get('/compras/consulta', [ConsultaComprasController::class, 'index'])->name('compras.consulta.index');
        Route::get('/compras/consulta/{id}', [ConsultaComprasController::class, 'show'])->name('compras.consulta.show')->whereNumber('id');
        Route::get('/compras', [ComprasController::class, 'create'])->name('compras.create');
        Route::post('/compras', [ComprasController::class, 'store'])->name('compras.store');
        Route::get('/compras/limpiar', [ComprasController::class, 'limpiar'])->name('compras.limpiar');
        Route::get('/compras/buscar-productos', [ComprasController::class, 'buscarProductos'])->name('compras.buscar-productos');
        Route::get('/compras/buscar-proveedores', [ComprasController::class, 'buscarProveedores'])->name('compras.buscar-proveedores');
        Route::get('/compras/carrito/partials', [ComprasController::class, 'partialsCarrito'])->name('compras.carrito.partials');
        Route::post('/compras/carrito/agregar', [ComprasController::class, 'agregarItem'])->name('compras.carrito.agregar');
        Route::post('/compras/carrito/quitar', [ComprasController::class, 'quitarItem'])->name('compras.carrito.quitar');
        Route::post('/compras/carrito/actualizar-cantidad', [ComprasController::class, 'actualizarCantidad'])->name('compras.carrito.actualizar-cantidad');
        Route::post('/compras/carrito/actualizar-precio', [ComprasController::class, 'actualizarPrecio'])->name('compras.carrito.actualizar-precio');
    });

    // Mantenimiento: Forma farmacéutica (Categoría)
    Route::resource('mantenimiento/categorias', MantenimientoCategoriaController::class)->names('mantenimiento.categorias');
    Route::resource('mantenimiento/presentaciones', MantenimientoPresentacionController::class)->names('mantenimiento.presentaciones');
    Route::resource('mantenimiento/sintomas', MantenimientoSintomaController::class)->names('mantenimiento.sintomas');
    Route::resource('mantenimiento/lotes', MantenimientoLoteController::class)->names('mantenimiento.lotes');
    Route::resource('mantenimiento/usuarios', MantenimientoUsuarioController::class)->names('mantenimiento.usuarios');

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
