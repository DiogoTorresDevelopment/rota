<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth Routes
Route::get('login', function () { 
    return view('pages.auth.login'); 
})->name('login');

Route::post('login', [AuthController::class, 'login'])->name('login.submit');

Route::get('register', function () { return view('pages.auth.register'); })->name('register');

Route::post('register', [AuthController::class, 'register'])->name('register.submit');

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rotas para Permissões
    Route::resource('permissions', PermissionGroupController::class);

    // Rotas para Usuários
    Route::middleware(['auth', 'permission:users.manage'])->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware(['auth', 'permission:users.view'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    Route::resource('drivers', DriverController::class);

    // Adicione estas novas rotas para documentos
    Route::post('drivers/documents', [DriverController::class, 'storeDocument'])->name('drivers.documents.store');
    Route::get('drivers/{driver}/documents', [DriverController::class, 'getDocuments'])->name('drivers.documents.index');
    Route::delete('drivers/documents/{document}', [DriverController::class, 'deleteDocument'])->name('drivers.documents.destroy');

    Route::group(['prefix' => 'email'], function(){
        Route::get('inbox', function () { return view('pages.email.inbox'); });
        Route::get('read', function () { return view('pages.email.read'); });
        Route::get('compose', function () { return view('pages.email.compose'); });
    });

    Route::group(['prefix' => 'apps'], function(){
        Route::get('chat', function () { return view('pages.apps.chat'); });
        Route::get('calendar', function () { return view('pages.apps.calendar'); });
    });

    Route::group(['prefix' => 'ui-components'], function(){
        Route::get('accordion', function () { return view('pages.ui-components.accordion'); });
        Route::get('alerts', function () { return view('pages.ui-components.alerts'); });
        Route::get('badges', function () { return view('pages.ui-components.badges'); });
        Route::get('breadcrumbs', function () { return view('pages.ui-components.breadcrumbs'); });
        Route::get('buttons', function () { return view('pages.ui-components.buttons'); });
        Route::get('button-group', function () { return view('pages.ui-components.button-group'); });
        Route::get('cards', function () { return view('pages.ui-components.cards'); });
        Route::get('carousel', function () { return view('pages.ui-components.carousel'); });
        Route::get('collapse', function () { return view('pages.ui-components.collapse'); });
        Route::get('dropdowns', function () { return view('pages.ui-components.dropdowns'); });
        Route::get('list-group', function () { return view('pages.ui-components.list-group'); });
        Route::get('media-object', function () { return view('pages.ui-components.media-object'); });
        Route::get('modal', function () { return view('pages.ui-components.modal'); });
        Route::get('navs', function () { return view('pages.ui-components.navs'); });
        Route::get('navbar', function () { return view('pages.ui-components.navbar'); });
        Route::get('pagination', function () { return view('pages.ui-components.pagination'); });
        Route::get('popovers', function () { return view('pages.ui-components.popovers'); });
        Route::get('progress', function () { return view('pages.ui-components.progress'); });
        Route::get('scrollbar', function () { return view('pages.ui-components.scrollbar'); });
        Route::get('scrollspy', function () { return view('pages.ui-components.scrollspy'); });
        Route::get('spinners', function () { return view('pages.ui-components.spinners'); });
        Route::get('tabs', function () { return view('pages.ui-components.tabs'); });
        Route::get('tooltips', function () { return view('pages.ui-components.tooltips'); });
    });

    Route::group(['prefix' => 'advanced-ui'], function(){
        Route::get('cropper', function () { return view('pages.advanced-ui.cropper'); });
        Route::get('owl-carousel', function () { return view('pages.advanced-ui.owl-carousel'); });
        Route::get('sortablejs', function () { return view('pages.advanced-ui.sortablejs'); });
        Route::get('sweet-alert', function () { return view('pages.advanced-ui.sweet-alert'); });
    });

    Route::group(['prefix' => 'forms'], function(){
        Route::get('basic-elements', function () { return view('pages.forms.basic-elements'); });
        Route::get('advanced-elements', function () { return view('pages.forms.advanced-elements'); });
        Route::get('editors', function () { return view('pages.forms.editors'); });
        Route::get('wizard', function () { return view('pages.forms.wizard'); });
    });

    Route::group(['prefix' => 'charts'], function(){
        Route::get('apex', function () { return view('pages.charts.apex'); });
        Route::get('chartjs', function () { return view('pages.charts.chartjs'); });
        Route::get('flot', function () { return view('pages.charts.flot'); });
        Route::get('peity', function () { return view('pages.charts.peity'); });
        Route::get('sparkline', function () { return view('pages.charts.sparkline'); });
    });

    Route::group(['prefix' => 'tables'], function(){
        Route::get('basic-tables', function () { return view('pages.tables.basic-tables'); });
        Route::get('data-table', function () { return view('pages.tables.data-table'); });
    });

    Route::group(['prefix' => 'icons'], function(){
        Route::get('feather-icons', function () { return view('pages.icons.feather-icons'); });
        Route::get('mdi-icons', function () { return view('pages.icons.mdi-icons'); });
    });

    Route::group(['prefix' => 'general'], function(){
        Route::get('blank-page', function () { return view('pages.general.blank-page'); });
        Route::get('faq', function () { return view('pages.general.faq'); });
        Route::get('invoice', function () { return view('pages.general.invoice'); });
        Route::get('profile', function () { return view('pages.general.profile'); });
        Route::get('pricing', function () { return view('pages.general.pricing'); });
        Route::get('timeline', function () { return view('pages.general.timeline'); });
    });

    Route::group(['prefix' => 'auth'], function(){
        Route::get('login', function () { return view('pages.auth.login'); });
        Route::get('register', function () { return view('pages.auth.register'); });
    });

    Route::group(['prefix' => 'error'], function(){
        Route::get('404', function () { return view('pages.error.404'); });
        Route::get('500', function () { return view('pages.error.500'); });
    });

    Route::get('/clear-cache', function() {
        Artisan::call('cache:clear');
        return "Cache is cleared";
    });

    // Mova estas rotas ANTES da rota catch-all 404
    Route::resource('trucks', TruckController::class);
    Route::resource('routes', RouteController::class);
    Route::resource('deliveries', DeliveryController::class);
    Route::post('deliveries/{delivery}/complete', [DeliveryController::class, 'complete'])
        ->name('deliveries.complete');
    Route::get('deliveries/{delivery}/details', [DeliveryController::class, 'details'])
        ->name('deliveries.details');
    Route::post('deliveries/{delivery}/reuse', [DeliveryController::class, 'reuse'])
        ->name('deliveries.reuse');

    // A rota catch-all 404 deve ser a última
    Route::any('/{page?}',function(){
        return View::make('pages.error.404');
    })->where('page','.*');
});

// Rotas de Permissões
Route::middleware(['auth', 'permission:permissions.view'])->group(function () {
    Route::get('/permissions', [PermissionGroupController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{permission}', [PermissionGroupController::class, 'show'])->name('permissions.show');
});

Route::middleware(['auth', 'permission:permissions.manage'])->group(function () {
    Route::get('/permissions/create', [PermissionGroupController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionGroupController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{permission}/edit', [PermissionGroupController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{permission}', [PermissionGroupController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{permission}', [PermissionGroupController::class, 'destroy'])->name('permissions.destroy');
});
