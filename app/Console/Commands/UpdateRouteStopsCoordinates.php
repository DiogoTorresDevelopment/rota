<?php

namespace App\Console\Commands;

use App\Models\RouteStop;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class UpdateRouteStopsCoordinates extends Command
{
    protected $signature = 'route:update-coordinates';
    protected $description = 'Atualiza as coordenadas de todas as paradas de rota';

    public function handle(GeocodingService $geocoding)
    {
        $stops = RouteStop::whereNull('latitude')->orWhereNull('longitude')->get();
        $bar = $this->output->createProgressBar(count($stops));

        foreach ($stops as $stop) {
            $coordinates = $geocoding->getCoordinates($stop->full_address);
            
            if ($coordinates) {
                $stop->update([
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude']
                ]);
                
                // Importante: adicionar delay para respeitar limites da API
                sleep(1);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nCoordenadas atualizadas com sucesso!");
    }
} 