<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NumberController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RaffleConfigController;

// Ruta Pública (sin autenticación)
Route::get('/', [PublicController::class, 'index'])->name('public.home');
Route::get('/api/numbers/status', [PublicController::class, 'numbersStatus'])->name('api.numbers.status');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('dashboard');

    // Gestión de Números
    Route::get('/numeros', [NumberController::class, 'index'])->name('numbers.index');
    Route::get('/numeros/{number}', [NumberController::class, 'show'])->name('numbers.show');
    Route::post('/numeros/{number}/vender', [NumberController::class, 'sell'])->name('numbers.sell');
    Route::put('/numeros/{number}/update', [NumberController::class, 'update'])->name('numbers.update');

    // Gestión de Clientes
    Route::resource('clientes', ClienteController::class);

    // Gestión de Pagos
    Route::post('/pagos', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/clientes/{cliente}/pagos', [PaymentController::class, 'index'])->name('payments.index');

    // Reportes
    Route::get('/reportes', [VendorController::class, 'reports'])->name('reports');
    // En routes/web.php, agrega estas rutas dentro del grupo auth:
    Route::get('/export-report', [VendorController::class, 'exportReport'])->name('export.report');
    Route::get('/today-sales', [VendorController::class, 'todaySales'])->name('today.sales');

    // Configuración de la Rifa (solo edición)
    Route::get('/raffle-config', [RaffleConfigController::class, 'edit'])->name('raffle-config.edit');
    Route::put('/raffle-config', [RaffleConfigController::class, 'update'])->name('raffle-config.update');
});
