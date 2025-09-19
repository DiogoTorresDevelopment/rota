<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\DriverRepositoryInterface;
use App\Repositories\Eloquent\DriverRepository;
use App\Repositories\Contracts\PassengerRepositoryInterface;
use App\Repositories\Eloquent\PassengerRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind repository interfaces to their implementations
        $this->app->bind(
            DriverRepositoryInterface::class,
            DriverRepository::class
        );

        $this->app->bind(
            PassengerRepositoryInterface::class,
            PassengerRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
