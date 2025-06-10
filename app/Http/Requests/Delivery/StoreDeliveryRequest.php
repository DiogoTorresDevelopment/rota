<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeliveryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'route_id' => 'required|exists:routes,id',
            'driver_id' => 'required|exists:drivers,id',
            'truck_id' => 'required|exists:trucks,id',
            'carroceria_ids' => 'required|array|min:1',
            'carroceria_ids.*' => 'exists:carrocerias,id'
            'carroceria_id' => 'nullable|exists:carrocerias,id'
        ];
    }

    public function messages()
    {
        return [
            'route_id.required' => 'A rota é obrigatória',
            'route_id.exists' => 'A rota selecionada não existe',
            'driver_id.required' => 'O motorista é obrigatório',
            'driver_id.exists' => 'Motorista inválido',
            'truck_id.required' => 'O caminhão é obrigatório',
            'truck_id.exists' => 'Caminhão inválido',
            'carroceria_ids.required' => 'É necessário selecionar ao menos uma carroceria',
            'carroceria_ids.*.exists' => 'Carroceria inválida'
            'carroceria_id.exists' => 'Carroceria inválida'
        ];
    }
} 