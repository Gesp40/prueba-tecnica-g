<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\SelectsController;
use App\Http\Controllers\ReportesController;
use Inertia\Inertia;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('registros.create')
        : redirect()->route('login');
})->name('root');

// Rutas de autenticación
if (file_exists(base_path('routes/auth.php'))) {
    require __DIR__ . '/auth.php';
}

// Rutas protegidas de la app (solo auth; sin verificación de email para la prueba)
Route::middleware('auth')->group(function () {

    // Formulario principal
    Route::get('/registros/create', [RegistroController::class, 'create'])->name('registros.create');
    Route::post('/registros', [RegistroController::class, 'store'])->name('registros.store');
    Route::get('/registros', [RegistroController::class, 'index'])->name('registros.index');
    Route::get('/registros/{registro}/editar', [RegistroController::class, 'edit'])->name('registros.edit');
    Route::put('/registros/{registro}', [RegistroController::class, 'update'])->name('registros.update');
    Route::delete('/registros/{registro}', [RegistroController::class, 'destroy'])->name('registros.destroy');

    // Endpoints para selects anidados (JSON)
    Route::prefix('api')->group(function () {
        Route::get('/proyectos', [SelectsController::class, 'proyectos'])->name('api.proyectos');
        Route::get('/proyectos/{proyecto}/bloques', [SelectsController::class, 'bloques'])->name('api.proyectos.bloques');
        Route::get('/bloques/{bloque}/piezas', [SelectsController::class, 'piezas'])->name('api.bloques.piezas');

        // Reportes (JSON)
        Route::get('/reportes/pendientes', [ReportesController::class, 'apiPendientesPorProyecto'])->name('api.reportes.pendientes');
        Route::get('/reportes/estadisticas', [ReportesController::class, 'apiEstadisticas'])->name('api.reportes.estadisticas');
    });

    // Vistas Inertia de reportes
    Route::get('/reportes/pendientes', [ReportesController::class, 'pendientes'])->name('reportes.pendientes');
    Route::get('/reportes/estadisticas', [ReportesController::class, 'estadisticas'])->name('reportes.estadisticas');
    Route::get('/reportes/pendientes-detalle', function () {
        return Inertia::render('Reportes/PendientesDetalle');
    })->name('reportes.pendientes-detalle');

    // APIs para reportes
    Route::get('/api/reportes/pendientes', [ReportesController::class, 'pendientesApi']);
    Route::get('/api/reportes/estadisticas', [ReportesController::class, 'apiEstadisticas']);
    Route::get('/api/reportes/pendientes-detalle', [ReportesController::class, 'apiPendientesDetalle']);
});

Route::middleware('auth')->group(function () {
    // Vistas de reportes
    Route::get('/reportes', function () {
        return Inertia::render('Reportes/Pendientes');
    })->name('reportes.pendientes');

    Route::get('/reportes/estadisticas', function () {
        return Inertia::render('Reportes/Estadisticas');
    })->name('reportes.estadisticas');

    Route::get('/reportes/pendientes-detalle', function () {
        return Inertia::render('Reportes/PendientesDetalle');
    })->name('reportes.pendientes-detalle');
});
