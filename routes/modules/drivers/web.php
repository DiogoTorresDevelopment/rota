<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Drivers\Controllers\DriverController;
use App\Modules\Drivers\Controllers\DriverDocumentController;

/*
|--------------------------------------------------------------------------
| Driver Module Routes
|--------------------------------------------------------------------------
|
| Routes related to driver management functionality
|
*/

Route::middleware(['web', 'auth'])->prefix('drivers')->name('drivers.')->group(function () {
    
    // Driver CRUD operations
    Route::get('/', [DriverController::class, 'index'])->name('index');
    Route::get('/create', [DriverController::class, 'create'])->name('create');
    Route::post('/', [DriverController::class, 'store'])->name('store');
    Route::get('/{id}', [DriverController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [DriverController::class, 'edit'])->name('edit');
    Route::put('/{id}', [DriverController::class, 'update'])->name('update');
    Route::delete('/{id}', [DriverController::class, 'destroy'])->name('destroy');
    
    // Driver documents management
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::post('/', [DriverDocumentController::class, 'store'])->name('store');
        Route::get('/{driverId}', [DriverDocumentController::class, 'index'])->name('index');
        Route::delete('/{documentId}', [DriverDocumentController::class, 'destroy'])->name('destroy');
    });
});