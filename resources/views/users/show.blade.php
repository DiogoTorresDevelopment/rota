@extends('layout.master')

@section('content')
<div class="flex flex-col">
  <!-- Breadcrumb -->
  <nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
      <li class="inline-flex items-center">
        <a href="#" class="text-gray-700 hover:text-blue-600">Configurações</a>
      </li>
      <li>
        <div class="flex items-center">
          <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
          </svg>
          <a href="{{ route('users.index') }}" class="text-gray-700 hover:text-blue-600">Usuários</a>
        </div>
      </li>
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
          </svg>
          <span class="text-gray-500">Detalhes do Usuário</span>
        </div>
      </li>
    </ol>
  </nav>

  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Detalhes do usuário</h2>
        <p class="mt-1 text-sm text-gray-600">Informações detalhadas do usuário.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h3 class="text-sm font-medium text-gray-500">Nome</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-gray-500">E-mail</h3>
          <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-gray-500">Grupo de Permissão</h3>
          <p class="mt-1 text-sm text-gray-900">
            @if($user->permissionGroups->first())
              <span class="badge bg-info">{{ $user->permissionGroups->first()->name }}</span>
            @else
              <span class="text-gray-500">Sem grupo</span>
            @endif
          </p>
        </div>

        <div>
          <h3 class="text-sm font-medium text-gray-500">Status</h3>
          <p class="mt-1">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
              {{ $user->status ? 'Ativo' : 'Inativo' }}
            </span>
          </p>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="mt-6 flex justify-end space-x-2">
        <a href="{{ route('users.index') }}" 
           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Voltar
        </a>
        @if(hasPermission('users.manage'))
        <a href="{{ route('users.edit', $user->id) }}" 
           class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
          Editar
        </a>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection 