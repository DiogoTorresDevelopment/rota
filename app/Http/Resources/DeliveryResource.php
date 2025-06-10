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
            'driver' => $this->deliveryDriver ? [
                'id' => $this->original_driver_id,
                'name' => $this->deliveryDriver->name,
                'cpf' => $this->deliveryDriver->cpf,
                'phone' => $this->deliveryDriver->phone,
                'email' => $this->deliveryDriver->email,
                'cep' => $this->deliveryDriver->cep,
                'state' => $this->deliveryDriver->state,
                'city' => $this->deliveryDriver->city,
                'street' => $this->deliveryDriver->street,
                'number' => $this->deliveryDriver->number,
                'district' => $this->deliveryDriver->district,
            ] : null,
            'truck' => $this->deliveryTruck ? [
                'id' => $this->original_truck_id,
                'marca' => $this->deliveryTruck->marca,
                'modelo' => $this->deliveryTruck->modelo,
                'placa' => $this->deliveryTruck->placa,
                'chassi' => $this->deliveryTruck->chassi,
                'ano' => $this->deliveryTruck->ano,
                'cor' => $this->deliveryTruck->cor,
                'tipo_combustivel' => $this->deliveryTruck->tipo_combustivel,
                'carga_suportada' => $this->deliveryTruck->carga_suportada,
                'quilometragem' => $this->deliveryTruck->quilometragem,
                'ultima_revisao' => $this->deliveryTruck->ultima_revisao,
            ] : null,
            'carrocerias' => $this->deliveryCarrocerias->map(function($c){
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
                'name' => $this->currentStop->deliveryRouteStop->name,
            ] : null,
            'route' => [
                'id' => $this->original_route_id,
                'name' => $this->deliveryRoute->name,
                'stops' => $this->deliveryRoute->stops->map(function($stop) {
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

