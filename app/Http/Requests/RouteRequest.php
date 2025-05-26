<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $step = $this->input('step', 1);
        
        $rules = [
            // Step 1 - Informações básicas
            'step1' => [
                'name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'driver_id' => 'required|exists:drivers,id',
                'truck_id' => 'required|exists:trucks,id',
                'current_mileage' => 'required|numeric|min:0',
            ],
            
            // Step 2 - Endereços
            'step2' => [
                'origin.name' => 'required|string|max:255',
                'origin.schedule' => 'required|date_format:H:i',
                'origin.cep' => 'required|string|size:9',
                'origin.state' => 'required|string|size:2',
                'origin.city' => 'required|string|max:255',
                'origin.street' => 'required|string|max:255',
                'origin.number' => 'required|string|max:20',
                'origin.latitude' => 'nullable|numeric|between:-90,90',
                'origin.longitude' => 'nullable|numeric|between:-180,180',
                
                'destination.name' => 'required|string|max:255',
                'destination.cep' => 'required|string|size:9',
                'destination.state' => 'required|string|size:2',
                'destination.city' => 'required|string|max:255',
                'destination.street' => 'required|string|max:255',
                'destination.number' => 'required|string|max:20',
                'destination.latitude' => 'nullable|numeric|between:-90,90',
                'destination.longitude' => 'nullable|numeric|between:-180,180',
            ],
            
            // Step 3 - Destinos intermediários
            'step3' => [
                'stops' => 'array',
                'stops.*.name' => 'required|string|max:255',
                'stops.*.cep' => 'required|string|size:9',
                'stops.*.state' => 'required|string|size:2',
                'stops.*.city' => 'required|string|max:255',
                'stops.*.street' => 'required|string|max:255',
                'stops.*.number' => 'required|string|max:20',
                'stops.*.order' => 'required|integer|min:1',
                'stops.*.latitude' => 'nullable|numeric|between:-90,90',
                'stops.*.longitude' => 'nullable|numeric|between:-180,180',
            ],
        ];

        return $rules['step' . $step] ?? [];
    }

    public function messages()
    {
        return [
            'name.required' => 'O nome da rota é obrigatório',
            'start_date.required' => 'A data de início é obrigatória',
            'driver_id.required' => 'O motorista é obrigatório',
            'truck_id.required' => 'O caminhão é obrigatório',
            'current_mileage.required' => 'A quilometragem atual é obrigatória',
            // ... adicione mais mensagens conforme necessário
        ];
    }
} 