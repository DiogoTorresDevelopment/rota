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
            'route_id' => 'required|exists:routes,id'
        ];
    }

    public function messages()
    {
        return [
            'route_id.required' => 'A rota é obrigatória',
            'route_id.exists' => 'A rota selecionada não existe'
        ];
    }
} 