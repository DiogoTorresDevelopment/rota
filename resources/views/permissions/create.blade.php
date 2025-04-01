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
          <a href="{{ route('permissions.index') }}" class="text-gray-700 hover:text-blue-600">Permissões</a>
        </div>
      </li>
      <li aria-current="page">
        <div class="flex items-center">
          <svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
          </svg>
          <span class="text-gray-500">Novo Grupo</span>
        </div>
      </li>
    </ol>
  </nav>

  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Dados das permissões</h2>
        <p class="mt-1 text-sm text-gray-600">Estas informações serão utilizadas dentro do sistema.</p>
      </div>

      <form action="{{ route('permissions.store') }}" method="POST">
        @csrf
        
        <!-- Grid de 2 colunas para Nome e Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Nome do grupo -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
              Nome do grupo de permissões
            </label>
            <input type="text" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}"
                   placeholder="Ex: Operador">
            @error('name')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
              Status
            </label>
            <select class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror" 
                    id="status" 
                    name="status">
              <option value="ativo" {{ old('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
              <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
            </select>
            @error('status')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <!-- Seção de Permissões -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Permissões</label>
          <div class="border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Coluna de Gerenciamento -->
              <div>
                <h3 class="font-medium text-gray-900 mb-3">Gerenciamento</h3>
                <div class="space-y-3">
                  @foreach($managementPermissions as $permission)
                  <label class="flex items-center">
                    <input type="checkbox" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                           id="permission_{{ $permission->id }}" 
                           name="permissions[]" 
                           value="{{ $permission->id }}">
                    <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                  </label>
                  @endforeach
                </div>
              </div>

              <!-- Coluna Operacional -->
              <div>
                <h3 class="font-medium text-gray-900 mb-3">Operacional</h3>
                <div class="space-y-3">
                  @foreach($operationalPermissions as $permission)
                  <label class="flex items-center">
                    <input type="checkbox" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                           id="permission_{{ $permission->id }}" 
                           name="permissions[]" 
                           value="{{ $permission->id }}">
                    <span class="ml-2 text-sm text-gray-700">{{ $permission->name }}</span>
                  </label>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
          @error('permissions')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end space-x-2">
          <a href="{{ route('permissions.index') }}" 
             class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Cancelar
          </a>
          <button type="submit" 
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Salvar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection 