<?php

namespace App\Services;

use App\Models\Route;
use App\Models\RouteAddress;
use App\Models\RouteStop;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RouteService
{
    protected $routeOptimizationService;

    public function __construct(RouteOptimizationService $routeOptimizationService)
    {
        $this->routeOptimizationService = $routeOptimizationService;
    }

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
            // Geocodifica endereço de origem
            $originAddress = "{$data['origin']['street']}, {$data['origin']['number']}, {$data['origin']['city']}, {$data['origin']['state']}, {$data['origin']['cep']}";
            $originGeocode = $this->routeOptimizationService->geocodeAddress($originAddress);

            // Geocodifica endereço de destino
            $destinationAddress = "{$data['destination']['street']}, {$data['destination']['number']}, {$data['destination']['city']}, {$data['destination']['state']}, {$data['destination']['cep']}";
            $destinationGeocode = $this->routeOptimizationService->geocodeAddress($destinationAddress);

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
                    'latitude' => $originGeocode['latitude'] ?? null,
                    'longitude' => $originGeocode['longitude'] ?? null,
                    'place_id' => $originGeocode['place_id'] ?? null,
                    'formatted_address' => $originGeocode['formatted_address'] ?? null,
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
                    'latitude' => $destinationGeocode['latitude'] ?? null,
                    'longitude' => $destinationGeocode['longitude'] ?? null,
                    'place_id' => $destinationGeocode['place_id'] ?? null,
                    'formatted_address' => $destinationGeocode['formatted_address'] ?? null,
                ]
            );

            return $route;
        });
    }

    public function saveStep3($data, $routeId = null)
    {
        try {
            DB::beginTransaction();

            $route = $routeId ? Route::findOrFail($routeId) : Route::latest()->first();
            
            // Remove todos os stops existentes
            $route->stops()->delete();

            // Adiciona os novos stops
            foreach ($data['destinations'] as $index => $destination) {
                // Se não tiver coordenadas, tenta geocodificar
                if (empty($destination['latitude']) || empty($destination['longitude'])) {
                    $stopAddress = "{$destination['street']}, {$destination['number']}, {$destination['city']}, {$destination['state']}, {$destination['cep']}";
                    $geocode = $this->routeOptimizationService->geocodeAddress($stopAddress);
                    
                    if ($geocode) {
                        $destination['latitude'] = $geocode['latitude'];
                        $destination['longitude'] = $geocode['longitude'];
                    }
                }

                $route->stops()->create([
                    'name' => $destination['name'],
                    'cep' => $destination['cep'],
                    'state' => $destination['state'],
                    'city' => $destination['city'],
                    'street' => $destination['street'],
                    'number' => $destination['number'],
                    'order' => $index + 1,
                    'latitude' => $destination['latitude'] ?? null,
                    'longitude' => $destination['longitude'] ?? null
                ]);
            }

            // Atualiza o status da rota para active
            $route->update(['status' => 'active']);

            DB::commit();
            return $route;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar step 3: ' . $e->getMessage());
            throw $e;
        }
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