<?php

namespace App\Services;

use App\Models\Route;
use App\Models\RouteStop;
use App\Models\RouteAddress;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RouteOptimizationService
{
    protected $nominatimUrl = 'https://nominatim.openstreetmap.org/search';
    
    /**
     * Geocodifica um endereço usando Nominatim
     */
    public function geocodeAddress($address)
    {
        $cacheKey = 'geocode_' . md5($address);
        
        return Cache::remember($cacheKey, 86400, function () use ($address) {
            $response = Http::withHeaders([
                'User-Agent' => 'RotaApp/1.0'
            ])->get($this->nominatimUrl, [
                'q' => $address,
                'format' => 'json',
                'limit' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                return [
                    'latitude' => $data['lat'],
                    'longitude' => $data['lon'],
                    'place_id' => $data['place_id'],
                    'formatted_address' => $data['display_name']
                ];
            }

            return null;
        });
    }

    /**
     * Calcula a distância entre dois pontos usando a fórmula de Haversine
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Raio da Terra em km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta/2) * sin($latDelta/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta/2) * sin($lonDelta/2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

    /**
     * Otimiza a ordem das paradas em uma rota
     */
    public function optimizeRouteStops(Route $route)
    {
        // Obtém origem e destino
        $origin = $route->origin();
        $destination = $route->destination();
        
        if (!$origin || !$destination) {
            throw new \Exception('Origem e destino são obrigatórios para otimização');
        }

        // Obtém todas as paradas
        $stops = $route->stops;
        
        // Se não houver paradas, retorna a rota como está
        if ($stops->isEmpty()) {
            return $route;
        }

        // Garante que todas as paradas têm coordenadas
        foreach ($stops as $stop) {
            if (!$stop->latitude || !$stop->longitude) {
                $address = "{$stop->street}, {$stop->number}, {$stop->city}, {$stop->state}, {$stop->cep}";
                $geocode = $this->geocodeAddress($address);
                
                if ($geocode) {
                    $stop->update([
                        'latitude' => $geocode['latitude'],
                        'longitude' => $geocode['longitude']
                    ]);
                }
            }
        }

        // Implementa o algoritmo do vizinho mais próximo
        $optimizedStops = [];
        $remainingStops = $stops->toArray();
        $currentPoint = [
            'latitude' => $origin->latitude,
            'longitude' => $origin->longitude
        ];

        while (!empty($remainingStops)) {
            $nearestStop = null;
            $minDistance = PHP_FLOAT_MAX;

            foreach ($remainingStops as $key => $stop) {
                $distance = $this->calculateDistance(
                    $currentPoint['latitude'],
                    $currentPoint['longitude'],
                    $stop['latitude'],
                    $stop['longitude']
                );

                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearestStop = $stop;
                    $nearestKey = $key;
                }
            }

            if ($nearestStop) {
                $optimizedStops[] = $nearestStop;
                $currentPoint = [
                    'latitude' => $nearestStop['latitude'],
                    'longitude' => $nearestStop['longitude']
                ];
                unset($remainingStops[$nearestKey]);
            }
        }

        // Atualiza a ordem das paradas no banco de dados
        foreach ($optimizedStops as $index => $stop) {
            RouteStop::where('id', $stop['id'])->update(['order' => $index + 1]);
        }

        return $route->fresh(['stops']);
    }
} 