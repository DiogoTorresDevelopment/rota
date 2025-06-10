@extends('layout.master')

@section('content')
<div class="flex flex-col">
  <!-- Breadcrumb -->
  <nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
      <li class="inline-flex items-center">
        <a href="#" class="text-gray-700 hover:text-blue-600">Operacional</a>
      </li>
      <li>
        <div class="flex items-center">
          <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
          </svg>
          <a href="{{ route('deliveries.index') }}" class="text-gray-700 hover:text-blue-600">Entregas</a>
        </div>
      </li>
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
          </svg>
          <span class="text-gray-500">Detalhes da Entrega</span>
        </div>
      </li>
    </ol>
  </nav>

  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Detalhes da entrega</h2>
        <p class="mt-1 text-sm text-gray-600">Informações detalhadas da entrega.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Status</h3>
          <p class="mt-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
              @if($delivery->status === 'in_progress') bg-yellow-100 text-yellow-800
              @elseif($delivery->status === 'completed') bg-green-100 text-green-800
              @else bg-red-100 text-red-800 @endif">
              {{ $delivery->status === 'in_progress' ? 'Em andamento' : 
                 ($delivery->status === 'completed' ? 'Finalizada' : 'Cancelada') }}
            </span>
          </p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Rota</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $delivery->deliveryRoute->name }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Motorista</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $delivery->deliveryDriver->name }}</p>
          <p class="text-sm text-gray-500">{{ $delivery->deliveryDriver->cpf }}</p>
          <p class="text-sm text-gray-500">Telefone: {{ $delivery->deliveryDriver->phone }}</p>
          <p class="text-sm text-gray-500">Email: {{ $delivery->deliveryDriver->email }}</p>
          <p class="text-sm text-gray-500">CEP: {{ $delivery->deliveryDriver->cep }}</p>
          <p class="text-sm text-gray-500">Estado: {{ $delivery->deliveryDriver->state }}</p>
          <p class="text-sm text-gray-500">Cidade: {{ $delivery->deliveryDriver->city }}</p>
          <p class="text-sm text-gray-500">Rua: {{ $delivery->deliveryDriver->street }}</p>
          <p class="text-sm text-gray-500">Número: {{ $delivery->deliveryDriver->number }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Caminhão</h3>
          <p class="mt-1 text-sm text-gray-900">
            {{ $delivery->deliveryTruck->marca }} {{ $delivery->deliveryTruck->modelo }}
          </p>
          <p class="text-sm text-gray-500">Placa: {{ $delivery->deliveryTruck->placa }}</p>
          <p class="text-sm text-gray-500">Chassi: {{ $delivery->deliveryTruck->chassi }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Data de Início</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $delivery->start_date ? $delivery->start_date->format('d/m/Y H:i') : 'Não definida' }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Data de Término</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $delivery->end_date ? $delivery->end_date->format('d/m/Y H:i') : 'Não definida' }}</p>
        </div>
      </div>

      <!-- Carrocerias -->
      <div class="mt-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Carrocerias</h3>
        @if($delivery->deliveryCarrocerias->count() > 0)
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($delivery->deliveryCarrocerias as $carroceria)
              <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-gray-900">{{ $carroceria->descricao }}</p>
                <p class="text-sm text-gray-500">Placa: {{ $carroceria->placa }}</p>
                <p class="text-sm text-gray-500">Chassi: {{ $carroceria->chassi }}</p>
                <p class="text-sm text-gray-500">Peso Suportado: {{ $carroceria->peso_suportado }} kg</p>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-sm text-gray-500">Nenhuma carroceria atribuída</p>
        @endif
      </div>

      <!-- Paradas -->
      <div class="mt-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Paradas da Rota</h3>
        <div class="space-y-4">
          @foreach($delivery->deliveryStops()->orderBy('order')->get() as $stop)
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="flex items-center justify-between">
                <div>
                  <div class="flex items-center">
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-200 text-gray-700 text-sm font-medium mr-2">
                      {{ $stop->order }}
                    </span>
                    <p class="text-sm font-medium text-gray-900">{{ $stop->deliveryRouteStop->name }}</p>
                  </div>
                  <p class="mt-1 text-sm text-gray-500">
                    {{ $stop->deliveryRouteStop->street }}, {{ $stop->deliveryRouteStop->number }} - {{ $stop->deliveryRouteStop->city }}/{{ $stop->deliveryRouteStop->state }}
                  </p>
                </div>
                <div class="flex items-center">
                  @if($stop->status === 'completed')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Concluído
                    </span>
                  @elseif($stop->id === $delivery->current_delivery_stop_id)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      Em andamento
                    </span>
                  @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      Pendente
                    </span>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Botão Voltar -->
      <div class="mt-6 flex justify-end">
        <a href="{{ route('deliveries.index') }}" 
           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Voltar
        </a>
      </div>
    </div>
  </div>
</div>
@endsection 