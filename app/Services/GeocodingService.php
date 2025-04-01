<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public function getCoordinates(string $address): ?array
    {
        try {
            $response = Http::get('https://nominatim.openstreetmap.org/search', [
                'format' => 'json',
                'q' => $address,
                'limit' => 1,
                'addressdetails' => 1,
                'headers' => [
                    'User-Agent' => 'YourApp/1.0' // Importante: Nominatim requer um User-Agent
                ]
            ]);

            if ($response->successful() && count($response->json()) > 0) {
                $data = $response->json()[0];
                return [
                    'latitude' => $data['lat'],
                    'longitude' => $data['lon']
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Erro ao geocodificar endereço:', [
                'address' => $address,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
} 