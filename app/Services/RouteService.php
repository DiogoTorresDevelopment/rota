<?php

namespace App\Services;

use App\Models\Route;
use App\Models\RouteAddress;
use App\Models\RouteStop;
use Illuminate\Support\Facades\DB;

class RouteService
{
    public function saveStep1($data, $routeId = null)
    {
        return DB::transaction(function () use ($data, $routeId) {
            $route = Route::updateOrCreate(
                ['id' => $routeId],
                [
                    'name' => $data['name'],
                    'start_date' => $data['start_date'],
                    'driver_id' => $data['driver_id'],
                    'truck_id' => $data['truck_id'],
                    'current_mileage' => $data['current_mileage'],
                    'status' => 'draft'
                ]
            );
            
            return $route;
        });
    }

    public function saveStep2($data, Route $route)
    {
        return DB::transaction(function () use ($data, $route) {
            // Salvar origem
            RouteAddress::updateOrCreate(
                [
                    'route_id' => $route->id,
                    'type' => 'origin'
                ],
                [
                    'name' => $data['origin']['name'],
                    'schedule' => $data['origin']['schedule'],
                    'cep' => $data['origin']['cep'],
                    'state' => $data['origin']['state'],
                    'city' => $data['origin']['city'],
                    'street' => $data['origin']['street'],
                    'number' => $data['origin']['number'],
                    'complement' => $data['origin']['complement'] ?? null,
                ]
            );

            // Salvar destino
            RouteAddress::updateOrCreate(
                [
                    'route_id' => $route->id,
                    'type' => 'destination'
                ],
                [
                    'name' => $data['destination']['name'],
                    'cep' => $data['destination']['cep'],
                    'state' => $data['destination']['state'],
                    'city' => $data['destination']['city'],
                    'street' => $data['destination']['street'],
                    'number' => $data['destination']['number'],
                    'complement' => $data['destination']['complement'] ?? null,
                ]
            );

            return $route;
        });
    }

    public function saveStep3($data, Route $route)
    {
        return DB::transaction(function () use ($data, $route) {
            // Limpar paradas existentes
            $route->stops()->delete();
            
            // Adicionar novas paradas
            foreach ($data['stops'] as $stop) {
                $route->stops()->create([
                    'name' => $stop['name'],
                    'order' => $stop['order'],
                    'cep' => $stop['cep'],
                    'state' => $stop['state'],
                    'city' => $stop['city'],
                    'street' => $stop['street'],
                    'number' => $stop['number'],
                    'complement' => $stop['complement'] ?? null,
                ]);
            }

            return $route;
        });
    }

    public function delete(Route $route)
    {
        return DB::transaction(function () use ($route) {
            $route->addresses()->delete();
            $route->stops()->delete();
            return $route->delete();
        });
    }
} 