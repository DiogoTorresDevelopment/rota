<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // IMPORTANTE: redefinindo o limitador "api"
        RateLimiter::for('api', function (Request $request) {
            // Em ambiente de desenvolvimento, não há limite
            if (app()->environment('local', 'development')) {
                return Limit::none();
            }
            
            // Em produção: 300 requisições por minuto por IP
            return Limit::perMinute(300)->by($request->ip());
        });

        parent::boot();

        if (app()->runningInConsole() && app()->environment('local')) {
            $this->commands([
                \App\Console\Commands\ListApiRoutes::class
            ]);
        }
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        $middleware = ['api'];
        
        // Remove throttle em ambiente de desenvolvimento
        if (app()->environment('local', 'development')) {
            $middleware = array_diff($middleware, ['throttle:api']);
        }

        Route::prefix('api')
             ->middleware($middleware)
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
