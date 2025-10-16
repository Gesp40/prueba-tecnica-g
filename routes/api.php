<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportesController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Endpoint
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/reportes/pendientes-detalle', [ReportesController::class, 'apiPendientesDetalle']);
});
