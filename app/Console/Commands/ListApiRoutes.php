<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class ListApiRoutes extends Command
{
    protected $signature = 'api:routes';
    protected $description = 'Lista todas as rotas da API disponíveis';

    public function handle()
    {
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri(), 'api/');
        })->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'middleware' => implode('|', $route->middleware()),
            ];
        });

        $this->info('Rotas da API disponíveis:');
        $this->table(
            ['Método', 'URI', 'Nome', 'Middleware'],
            $routes->toArray()
        );

        \Log::info('Rotas da API listadas', [
            'routes' => $routes->toArray()
        ]);
    }
} 