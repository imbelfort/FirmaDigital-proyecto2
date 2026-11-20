<?php

use Illuminate\Support\Facades\Route;
use App\Features\Auth\AuthController;
use App\Features\Usuarios\UsuarioController;
use App\Features\Tramites\TramiteController;
use App\Features\Documentos\DocumentoController;
use App\Features\FirmaDigital\FirmaController;
use App\Features\SeguimientoSla\SeguimientoSlaController;
use App\Features\Reportes\ReporteController;
use App\Features\BusquedaGlobal\BusquedaGlobalController;

// Autenticación
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Búsqueda Global (Opciones + Registros)
Route::get('/search', [BusquedaGlobalController::class, 'search']);

// Usuarios
Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'store']);
    Route::put('/{id}', [UsuarioController::class, 'update']);
    Route::delete('/{id}', [UsuarioController::class, 'destroy']);
});

// Trámites
Route::prefix('tramites')->group(function () {
    Route::get('/', [TramiteController::class, 'index']);
    Route::get('/tipos', [TramiteController::class, 'tiposTramite']);
    Route::post('/', [TramiteController::class, 'store']);
    Route::post('/{id}/flujo', [TramiteController::class, 'avanzarFlujo']);
    Route::post('/{id}/observaciones', [TramiteController::class, 'agregarObservacion']);
    Route::post('/observaciones/{id}/subsanar', [TramiteController::class, 'subsanarObservacion']);
});

// Documentos del Expediente
Route::prefix('documentos')->group(function () {
    Route::post('/', [DocumentoController::class, 'store']);
    Route::post('/{id}/validar', [DocumentoController::class, 'validar']);
});

// Firma Digital y Despacho
Route::prefix('firma')->group(function () {
    Route::get('/delegaciones', [FirmaController::class, 'delegaciones']);
    Route::post('/tramite/{id}', [FirmaController::class, 'firmar']);
    Route::post('/despacho/{id}', [FirmaController::class, 'despachar']);
});

// SLA y Portal de Seguimiento
Route::prefix('seguimiento')->group(function () {
    Route::get('/monitoreo', [SeguimientoSlaController::class, 'monitoreo']);
    Route::get('/portal/{pin}', [SeguimientoSlaController::class, 'consultarPin']);
    Route::get('/verificar/{uuid}', [SeguimientoSlaController::class, 'verificarCertificado']);
});

// Reportes y Estadísticas
Route::prefix('reportes')->group(function () {
    Route::get('/dashboard', [ReporteController::class, 'dashboard']);
});
