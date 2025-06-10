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
            'carroceria_ids.*' => 'exists:carrocerias,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ];
    }

    public function messages()
    {
        return [
            'route_id.required' => 'A rota é obrigatória',
            'route_id.exists' => 'A rota selecionada não existe',
            'driver_id.required' => 'O motorista é obrigatório',
            'driver_id.exists' => 'O motorista selecionado não existe',
            'truck_id.required' => 'O caminhão é obrigatório',
            'truck_id.exists' => 'O caminhão selecionado não existe',
            'carroceria_ids.required' => 'Selecione pelo menos uma carroceria',
            'carroceria_ids.array' => 'Formato inválido para carrocerias',
            'carroceria_ids.min' => 'Selecione pelo menos uma carroceria',
            'carroceria_ids.*.exists' => 'Uma das carrocerias selecionadas não existe',
            'start_date.required' => 'A data de envio é obrigatória',
            'start_date.date' => 'A data de envio deve ser uma data válida',
            'end_date.date' => 'A data de entrega deve ser uma data válida',
            'end_date.after_or_equal' => 'A data de entrega deve ser igual ou posterior à data de envio'
        ];
    }
} 