<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'cep' => 'required|string|max:9',
            'state' => 'required|string|max:2',
            'city' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
        ];

        // Se for criação ou se os campos foram alterados, adiciona regra unique
        if (!$this->driver || $this->driver->cpf !== $this->cpf) {
            $rules['cpf'] = 'required|string|max:14|unique:drivers,cpf';
        }

        if (!$this->driver || $this->driver->email !== $this->email) {
            $rules['email'] = 'required|email|max:255|unique:drivers,email';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'email' => 'O campo :attribute deve ser um e-mail válido.',
            'unique' => 'Este :attribute já está cadastrado.',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'nome',
            'cpf' => 'CPF',
            'phone' => 'telefone',
            'email' => 'e-mail',
            'cep' => 'CEP',
            'state' => 'estado',
            'city' => 'cidade',
            'district' => 'bairro',
            'street' => 'rua',
            'number' => 'número',
        ];
    }
} 