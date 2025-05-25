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
          <span class="text-gray-500">Novo Usuário</span>
        </div>
      </li>
    </ol>
  </nav>

  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Dados do usuário</h2>
        <p class="mt-1 text-sm text-gray-600">Estas informações serão utilizadas dentro do sistema.</p>
      </div>

      <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        <!-- Grid de 2 colunas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <!-- Nome -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
              Nome
            </label>
            <input type="text" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}"
                   required>
            @error('name')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
              E-mail
            </label>
            <input type="email" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}"
                   required>
            @error('email')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Senha -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
              Senha
            </label>
            <input type="password" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                   id="password" 
                   name="password"
                   required>
            @error('password')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Confirmar Senha -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
              Confirmar Senha
            </label>
            <input type="password" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" 
                   id="password_confirmation" 
                   name="password_confirmation"
                   required>
          </div>
        </div>

        <!-- Grupos de Permissão -->
        <div class="mb-3">
          <label for="permission_group" class="form-label">Grupo de Permissão</label>
          <select class="form-select @error('permission_group') is-invalid @enderror" 
                  id="permission_group" 
                  name="permission_group">
            <option value="">Selecione um grupo</option>
            @foreach($permissionGroups as $group)
              <option value="{{ $group->id }}" {{ old('permission_group') == $group->id ? 'selected' : '' }}>
                {{ $group->name }}
              </option>
            @endforeach
          </select>
          @error('permission_group')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="text-muted">Selecione o grupo de permissão do usuário.</small>
        </div>

        <!-- Status -->
        <div class="mb-6">
          <div class="flex items-center">
            <input type="checkbox" 
                   id="status" 
                   name="status" 
                   value="1" 
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                   {{ old('status', true) ? 'checked' : '' }}>
            <label for="status" class="ml-2 text-sm font-medium text-gray-700">
              Ativo
            </label>
          </div>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end space-x-2">
          <a href="{{ route('users.index') }}" 
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

@push('plugin-scripts')
<script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
    $(document).ready(function() {
        $('select[name="permission_groups"]').select2({
            placeholder: "Selecione o grupo de permissão",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush 