<?php

namespace App\Observers;

use App\Models\RouteStop;
use App\Services\GeocodingService;

class RouteStopObserver
{
    protected $geocoding;

    public function __construct(GeocodingService $geocoding)
    {
        $this->geocoding = $geocoding;
    }

    public function creating(RouteStop $stop)
    {
        if (!$stop->latitude || !$stop->longitude) {
            $coordinates = $this->geocoding->getCoordinates($stop->full_address);
            if ($coordinates) {
                $stop->latitude = $coordinates['latitude'];
                $stop->longitude = $coordinates['longitude'];
            }
        }
    }
} 