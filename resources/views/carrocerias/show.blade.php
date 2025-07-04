@extends('layout.master')

@section('content')
<div class="flex flex-col">
  <!-- Breadcrumb -->
  <nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
      <li class="inline-flex items-center">
        <a href="#" class="text-gray-700 hover:text-blue-600">Cadastros</a>
      </li>
      <li>
        <div class="flex items-center">
          <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
          </svg>
          <a href="{{ route('carrocerias.index') }}" class="text-gray-700 hover:text-blue-600">Carrocerias</a>
        </div>
      </li>
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
          </svg>
          <span class="text-gray-500">Detalhes da Carroceria</span>
        </div>
      </li>
    </ol>
  </nav>

  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Detalhes da carroceria</h2>
        <p class="mt-1 text-sm text-gray-600">Informações detalhadas da carroceria.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Descrição</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $carroceria->descricao }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Chassi</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $carroceria->chassi }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Placa</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $carroceria->placa }}</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Peso Suportado</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $carroceria->peso_suportado }} kg</p>
        </div>
        <div>
          <h3 class="text-sm font-medium text-gray-500">Status</h3>
          <p class="mt-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $carroceria->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
              {{ $carroceria->status ? 'Ativa' : 'Inativa' }}
            </span>
          </p>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="mt-6 flex justify-end space-x-2">
        <a href="{{ route('carrocerias.index') }}" 
           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Voltar
        </a>
        <a href="{{ route('carrocerias.edit', $carroceria) }}" 
           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Editar
        </a>
      </div>
    </div>
  </div>
</div>
@endsection 