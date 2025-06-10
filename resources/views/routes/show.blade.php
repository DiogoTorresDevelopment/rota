@extends('layout.master')

@section('content')
<div class="flex flex-col">
  <!-- Breadcrumb -->
  <nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
      <li class="inline-flex items-center">
        <a href="#" class="text-gray-700 hover:text-blue-600">Rotas</a>
      </li>
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
          </svg>
          <span class="text-gray-500">Detalhes da Rota</span>
        </div>
      </li>
    </ol>
  </nav>

  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Detalhes da rota</h2>
        <p class="mt-1 text-sm text-gray-600">Informações detalhadas da rota.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Nome</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $route->name }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Data de Início</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $route->start_date ? $route->start_date->format('d/m/Y') : '-' }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Quilometragem Atual</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $route->current_mileage }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Status</h3>
          <p class="mt-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $route->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
              {{ $route->status }}
            </span>
          </p>
        </div>
      </div>

      <!-- Origem e Destino -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Origem</h3>
          @php $origin = $route->addresses->where('type', 'origin')->first(); @endphp
          @if($origin)
            <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $origin->name }}</p>
            <p class="text-sm text-gray-700">{{ $origin->street }}, {{ $origin->number }}<br>{{ $origin->city }} - {{ $origin->state }}<br>CEP: {{ $origin->cep }}</p>
            <p class="text-xs text-gray-500">Horário: {{ $origin->schedule ? \Carbon\Carbon::parse($origin->schedule)->format('H:i') : '-' }}</p>
          @else
            <span class="text-gray-500">-</span>
          @endif
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Destino</h3>
          @php $destination = $route->addresses->where('type', 'destination')->first(); @endphp
          @if($destination)
            <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $destination->name }}</p>
            <p class="text-sm text-gray-700">{{ $destination->street }}, {{ $destination->number }}<br>{{ $destination->city }} - {{ $destination->state }}<br>CEP: {{ $destination->cep }}</p>
          @else
            <span class="text-gray-500">-</span>
          @endif
        </div>
      </div>

      <!-- Paradas -->
      <div class="mb-6">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Paradas</h3>
        @if($route->stops->count())
          <ul class="list-disc list-inside text-sm text-gray-900">
            @foreach($route->stops->sortBy('order') as $stop)
              <li>
                <span class="font-semibold">{{ $stop->name }}</span> —
                {{ $stop->street }}, {{ $stop->number }}, {{ $stop->city }} - {{ $stop->state }} (CEP: {{ $stop->cep }})
              </li>
            @endforeach
          </ul>
        @else
          <span class="text-gray-500">Nenhuma parada cadastrada.</span>
        @endif
      </div>

      <!-- Botões de Ação -->
      <div class="mt-6 flex justify-end space-x-2">
        <a href="{{ route('routes.index') }}" 
           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Voltar
        </a>
        <a href="{{ route('routes.edit', $route) }}" 
           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Editar
        </a>
      </div>
    </div>
  </div>
</div>
@endsection 