<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TruckRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'ano' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'cor' => 'required|string|max:50',
            'tipo_combustivel' => 'required|string|max:50',
            'carga_suportada' => 'required|numeric|min:0',
            'quilometragem' => 'required|numeric|min:0',
            'placa' => [
                'required',
                'string',
                'size:7',
                'regex:/^[A-Z]{3}[0-9][A-Z][0-9]{2}$/',
                'unique:trucks,placa' . ($this->truck ? ',' . $this->truck->id : ''),
            ],
            'ultima_revisao' => 'required|date|before_or_equal:today',
            'status' => 'required|boolean',
        ];

        // Adiciona regra unique para chassi, exceto na edição
        if (!$this->truck || $this->truck->chassi !== $this->chassi) {
            $rules['chassi'] = 'required|string|max:17|unique:trucks,chassi';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max' => 'O campo :attribute não pode ter mais que :max caracteres.',
            'min' => 'O campo :attribute deve ser no mínimo :min.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'numeric' => 'O campo :attribute deve ser um número.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
            'unique' => 'Este :attribute já está em uso.',
            'ano.min' => 'O ano não pode ser anterior a 1900.',
            'ano.max' => 'O ano não pode ser superior a :max.',
            'carga_suportada.min' => 'A carga suportada não pode ser negativa.',
            'quilometragem.min' => 'A quilometragem não pode ser negativa.',
            'placa.required' => 'A placa é obrigatória.',
            'placa.size' => 'A placa deve ter exatamente 7 caracteres.',
            'placa.regex' => 'A placa deve estar no formato Mercosul (AAA0A00).',
            'placa.unique' => 'Esta placa já está cadastrada.',
            'ultima_revisao.required' => 'A data da última revisão é obrigatória.',
            'ultima_revisao.date' => 'A data da última revisão deve ser uma data válida.',
            'ultima_revisao.before_or_equal' => 'A data da última revisão não pode ser futura.',
        ];
    }

    public function attributes()
    {
        return [
            'marca' => 'marca',
            'modelo' => 'modelo',
            'ano' => 'ano',
            'cor' => 'cor',
            'tipo_combustivel' => 'tipo de combustível',
            'carga_suportada' => 'carga suportada',
            'chassi' => 'chassi',
            'quilometragem' => 'quilometragem',
            'status' => 'status',
            'placa' => 'placa',
            'ultima_revisao' => 'última revisão',
        ];
    }
} 