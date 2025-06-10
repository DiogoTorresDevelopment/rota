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
            'driver' => $this->driver ? [
                'id' => $this->driver->id,
                'name' => $this->driver->name,
            ] : null,
            'truck' => $this->truck ? [
                'id' => $this->truck->id,
                'marca' => $this->truck->marca,
                'modelo' => $this->truck->modelo,
            ] : null,
            'carrocerias' => $this->carrocerias->map(function($c){
                return [
                    'id' => $c->id,
                    'descricao' => $c->descricao,
                    'chassi' => $c->chassi,
                    'placa' => $c->placa,
                    'peso_suportado' => $c->peso_suportado,
                ];
            }),
            'current_stop' => $this->currentStop ? [
                'id' => $this->currentStop->id,
                'order' => $this->currentStop->order,
                'name' => $this->currentStop->routeStop->name,
            ] : null,
            'route' => [
                'id' => $this->route->id,
                'name' => $this->route->name,
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

