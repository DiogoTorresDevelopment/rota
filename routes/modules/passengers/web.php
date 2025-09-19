<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Passengers\Controllers\PassengerController;

/*
|--------------------------------------------------------------------------
| Passenger Module Routes
|--------------------------------------------------------------------------
|
| Routes related to passenger management functionality
|
*/

Route::middleware(['web', 'auth'])->prefix('passengers')->name('passengers.')->group(function () {
    
    // Passenger CRUD operations
    Route::get('/', [PassengerController::class, 'index'])->name('index');
    Route::get('/create', [PassengerController::class, 'create'])->name('create');
    Route::post('/', [PassengerController::class, 'store'])->name('store');
    Route::get('/{id}', [PassengerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PassengerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PassengerController::class, 'update'])->name('update');
    Route::delete('/{id}', [PassengerController::class, 'destroy'])->name('destroy');
    
    // Additional passenger-specific routes can be added here
    // Route::get('/active', [PassengerController::class, 'activePassengers'])->name('active');
});