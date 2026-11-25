<?php

use Illuminate\Support\Facades\Route;
use App\Features\Pagos\PagoController;

Route::post('/pagos/tramite/{id}/stripe', [PagoController::class, 'iniciarStripe']);
Route::post('/pagos/tramite/{id}/stripe/cancelar', [PagoController::class, 'cancelarStripe']);

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
