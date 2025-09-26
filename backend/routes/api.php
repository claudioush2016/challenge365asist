<?php
use App\Http\Controllers\Api\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('reservations')->group(function () {
    Route::post('/', [BookingController::class, 'store']); // Crea reserva
    Route::get('/', [BookingController::class, 'index']); // Lista reservas
    Route::get('/{id}', [BookingController::class, 'show']); // Detalle de reserva
    Route::put('/{id}/status', [BookingController::class, 'updateStatus']); // Cambia estado
});

// Ruta para detalles de un pasajero
Route::get('/passengers/{id}', [BookingController::class, 'showPassenger']);
