<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\DeliveryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes - Sem middleware de autenticação
Route::post('/login/driver', [AuthController::class, 'apiDriverLogin'])->name('api.driver.login');
Route::post('/login', [AuthController::class, 'apiLogin'])->name('api.login');
Route::post('/login-jwt', [AuthController::class, 'apiLoginJwt'])->name('api.login.jwt');
Route::post('/register-jwt', [AuthController::class, 'apiRegisterJwt'])->name('api.register.jwt');
Route::post('/password/forgot', [AuthController::class, 'apiForgotPassword'])->name('api.password.forgot');
Route::post('/password/reset', [AuthController::class, 'apiResetPassword'])->name('api.password.reset');
Route::get('/check-auth', [AuthController::class, 'checkAuth'])->name('api.check.auth');

// Rotas protegidas para motoristas
Route::middleware('driver.api')->group(function () {
    // Informações do motorista
    Route::get('/driver/profile', [DriverController::class, 'apiProfile']);
    Route::put('/driver/profile', [DriverController::class, 'apiUpdateProfile']);

    // Rotas do motorista
    Route::get('/driver/routes', [RouteController::class, 'apiDriverRoutes']);
    Route::get('/driver/routes/{route}', [RouteController::class, 'apiDriverRouteDetails']);

    // Entregas
    Route::get('/driver/deliveries', [DeliveryController::class, 'apiDriverDeliveries']);
    Route::get('/driver/deliveries/{delivery}', [DeliveryController::class, 'apiDriverDeliveryDetails']);
    Route::post('/driver/deliveries/{delivery}/complete', [DeliveryController::class, 'apiCompleteDelivery']);
    Route::post('/driver/deliveries/{delivery}/complete-stop', [DeliveryController::class, 'apiCompleteStop']);
    Route::post('/driver/upload-photo', [DeliveryController::class, 'apiUploadPhoto']);
    Route::delete('/driver/photos/{photo}', [DeliveryController::class, 'apiDeletePhoto']);

    // Logout
    Route::post('/logout', [AuthController::class, 'apiLogout']);
});

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});
