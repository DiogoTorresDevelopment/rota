<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'start_date' => $this->start_date?->format('d/m/Y H:i'),
            'end_date' => $this->end_date?->format('d/m/Y H:i'),
            'route' => [
                'id' => $this->route->id,
                'name' => $this->route->name,
                'driver' => [
                    'id' => $this->route->driver->id,
                    'name' => $this->route->driver->name,
                ],
                'stops' => $this->route->stops->map(function($stop) {
                    return [
                        'name' => $stop->name,
                        'street' => $stop->street,
                        'number' => $stop->number,
                        'city' => $stop->city,
                        'state' => $stop->state,
                        'order' => $stop->order,
                    ];
                })
            ]
        ];
    }
} 