@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
  <script src="https://unpkg.com/imask"></script>
@endpush

@section('content')
<div class="flex flex-col">
  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Dados do caminhão</h2>
        <p class="mt-1 text-sm text-gray-600">Estas informações serão utilizadas dentro do sistema.</p>
      </div>

      <form action="{{ route('trucks.store') }}" method="POST" id="truck-form">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <!-- Marca -->
          <div>
            <label for="marca" class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
            <input type="text" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('marca') border-red-500 @enderror" 
                   id="marca" 
                   name="marca" 
                   value="{{ old('marca') }}"
                   placeholder="Ex: Ford">
            @error('marca')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Modelo -->
          <div>
            <label for="modelo" class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
            <input type="text" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('modelo') border-red-500 @enderror" 
                   id="modelo" 
                   name="modelo" 
                   value="{{ old('modelo') }}"
                   placeholder="Ex: F-4000">
            @error('modelo')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Ano -->
          <div>
            <label for="ano" class="block text-sm font-medium text-gray-700 mb-1">Ano</label>
            <input type="number" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('ano') border-red-500 @enderror" 
                   id="ano" 
                   name="ano" 
                   value="{{ old('ano') }}"
                   placeholder="Ex: 2024">
            @error('ano')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Cor -->
          <div>
            <label for="cor" class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
            <input type="text" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('cor') border-red-500 @enderror" 
                   id="cor" 
                   name="cor" 
                   value="{{ old('cor') }}"
                   placeholder="Ex: Vermelho">
            @error('cor')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Tipo de Combustível -->
          <div>
            <label for="tipo_combustivel" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Combustível</label>
            <select class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('tipo_combustivel') border-red-500 @enderror" 
                    id="tipo_combustivel" 
                    name="tipo_combustivel">
              <option value="">Selecione...</option>
              <option value="Diesel" {{ old('tipo_combustivel') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
              <option value="Gasolina" {{ old('tipo_combustivel') == 'Gasolina' ? 'selected' : '' }}>Gasolina</option>
              <option value="Etanol" {{ old('tipo_combustivel') == 'Etanol' ? 'selected' : '' }}>Etanol</option>
              <option value="Flex" {{ old('tipo_combustivel') == 'Flex' ? 'selected' : '' }}>Flex</option>
            </select>
            @error('tipo_combustivel')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Carga Suportada -->
          <div>
            <label for="carga_suportada" class="block text-sm font-medium text-gray-700 mb-1">Carga Suportada (kg)</label>
            <input type="number" 
                   step="0.01" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('carga_suportada') border-red-500 @enderror" 
                   id="carga_suportada" 
                   name="carga_suportada" 
                   value="{{ old('carga_suportada') }}"
                   placeholder="Ex: 4000.00">
            @error('carga_suportada')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <!-- Chassi -->
          <div>
            <label for="chassi" class="block text-sm font-medium text-gray-700 mb-2">Chassi</label>
            <input type="text" 
                   name="chassi" 
                   id="chassi" 
                   class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Digite o chassi do caminhão">
          </div>

          <!-- Placa -->
          <div>
            <label for="placa" class="block text-sm font-medium text-gray-700 mb-2">Placa</label>
            <input type="text" 
                   name="placa" 
                   id="placa" 
                   class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 uppercase"
                   placeholder="Digite a placa do caminhão">
          </div>

          <!-- Quilometragem -->
          <div>
            <label for="quilometragem" class="block text-sm font-medium text-gray-700 mb-2">Quilometragem</label>
            <input type="number" 
                   name="quilometragem" 
                   id="quilometragem" 
                   step="0.01"
                   class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Digite a quilometragem atual">
          </div>

          <!-- Data da Última Revisão -->
          <div>
            <label for="ultima_revisao" class="block text-sm font-medium text-gray-700 mb-2">Data da Última Revisão</label>
            <input type="date" 
                   name="ultima_revisao" 
                   id="ultima_revisao" 
                   class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
          </div>

          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror" 
                    id="status" 
                    name="status">
              <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Ativo</option>
              <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inativo</option>
            </select>
            @error('status')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end space-x-2 mt-6">
          <a href="{{ route('trucks.index') }}" 
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
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Máscara para placa - Apenas padrão Mercosul
  const placaInput = document.getElementById('placa');
  const placaMask = IMask(placaInput, {
    mask: 'aaa0a00',
    definitions: {
      'a': {
        mask: /[A-Z]/
      },
      '0': {
        mask: /[0-9]/
      }
    },
    prepare: function(str) {
      return str.toUpperCase();
    }
  });

  // Resto do código do form submit
  const form = document.getElementById('truck-form');
  
  form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
      const response = await fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: new FormData(form)
      });

      const data = await response.json();

      if (response.ok && data.success) {
        await Swal.fire({
          title: 'Sucesso!',
          text: data.message,
          icon: 'success',
          confirmButtonText: 'Ok'
        });
        
        window.location.href = "{{ route('trucks.index') }}";
      } else {
        let errorMessage = data.message;
        if (data.errors) {
          errorMessage = Object.values(data.errors).flat().join('\n');
        }
        throw new Error(errorMessage);
      }
    } catch (error) {
      await Swal.fire({
        title: 'Erro!',
        text: error.message,
        icon: 'error',
        confirmButtonText: 'Ok'
      });
    }
  });
});
</script>
@endpush 