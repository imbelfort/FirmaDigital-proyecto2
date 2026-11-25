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
use App\Features\Pagos\PagoController;
use App\Features\Auditoria\AuditoriaController;

// Autenticación (Pública)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Contador de Visitas por Página (Público)
Route::post('/visitas/registrar', [AuditoriaController::class, 'registrarVisita']);

// Bitácora de Auditoría del Sistema (Exclusivo Administrador)
Route::prefix('auditoria')->group(function () {
    Route::get('/', [AuditoriaController::class, 'index']);
});

// Búsqueda Global (Opciones + Registros)
Route::get('/search', [BusquedaGlobalController::class, 'search']);

// Usuarios (Crear/Modificar usuarios restringido a Administrador General)
Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'store'])->middleware('rbac:ADMINISTRATIVO');
    Route::put('/{id}', [UsuarioController::class, 'update'])->middleware('rbac:ADMINISTRATIVO');
    Route::delete('/{id}', [UsuarioController::class, 'destroy'])->middleware('rbac:ADMINISTRATIVO');
});

// Trámites
Route::prefix('tramites')->group(function () {
    Route::get('/', [TramiteController::class, 'index']);
    Route::get('/tipos', [TramiteController::class, 'tiposTramite']);
    Route::post('/tipos', [TramiteController::class, 'storeTipoTramite'])->middleware('rbac:ADMINISTRATIVO');
    Route::put('/tipos/{id}', [TramiteController::class, 'updateTipoTramite'])->middleware('rbac:ADMINISTRATIVO');
    Route::delete('/tipos/{id}', [TramiteController::class, 'destroyTipoTramite'])->middleware('rbac:ADMINISTRATIVO');
    Route::post('/', [TramiteController::class, 'store'])->middleware('rbac:ALUMNO,DOCENTE,ADMINISTRATIVO');
    Route::post('/{id}/flujo', [TramiteController::class, 'avanzarFlujo'])->middleware('rbac:ENCARGADO,ADMINISTRATIVO');
    Route::post('/{id}/observaciones', [TramiteController::class, 'agregarObservacion'])->middleware('rbac:ENCARGADO,ADMINISTRATIVO');
    Route::post('/observaciones/{id}/subsanar', [TramiteController::class, 'subsanarObservacion']);
});

// Pagos (PagoFacil QR)
Route::prefix('pagos')->group(function () {
    Route::post('/tramite/{id}/generar-qr', [PagoController::class, 'generarQr']);
    Route::post('/tramite/{id}/verificar', [PagoController::class, 'verificarEstadoPago']);
    Route::post('/tramite/{id}/confirmar', [PagoController::class, 'confirmarPago']);
    Route::post('/callback', [PagoController::class, 'callback']);
});

// Documentos del Expediente
Route::prefix('documentos')->group(function () {
    Route::post('/', [DocumentoController::class, 'store'])->middleware('rbac:ALUMNO,ENCARGADO,ADMINISTRATIVO');
    Route::post('/{id}/validar', [DocumentoController::class, 'validar'])->middleware('rbac:ENCARGADO,ADMINISTRATIVO');
});

// Firma Digital y Despacho (Exclusivo Vicerrector y Encargado/Admin)
Route::prefix('firma')->group(function () {
    Route::get('/delegaciones', [FirmaController::class, 'delegaciones']);
    Route::post('/tramite/{id}', [FirmaController::class, 'firmar'])->middleware('rbac:VICERRECTOR,ADMINISTRATIVO');
    Route::post('/despacho/{id}', [FirmaController::class, 'despachar'])->middleware('rbac:ENCARGADO,ADMINISTRATIVO');
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
