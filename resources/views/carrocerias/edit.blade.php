@extends('layout.master')

@section('content')
<div class="flex flex-col">
  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Dados da Carroceria</h2>
        <p class="mt-1 text-sm text-gray-600">Atualize as informações da carroceria conforme necessário.</p>
      </div>

      <form action="{{ route('carrocerias.update', $carroceria) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div>
            <label for="descricao" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <input type="text" 
                   name="descricao" 
                   id="descricao" 
                   value="{{ $carroceria->descricao }}" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('descricao') border-red-500 @enderror" 
                   required>
            @error('descricao')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="chassi" class="block text-sm font-medium text-gray-700 mb-1">Chassi</label>
            <input type="text" 
                   name="chassi" 
                   id="chassi" 
                   value="{{ $carroceria->chassi }}" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('chassi') border-red-500 @enderror" 
                   required>
            @error('chassi')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="placa" class="block text-sm font-medium text-gray-700 mb-1">Placa</label>
            <input type="text" 
                   name="placa" 
                   id="placa" 
                   value="{{ $carroceria->placa }}" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('placa') border-red-500 @enderror uppercase" 
                   required>
            @error('placa')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="peso_suportado" class="block text-sm font-medium text-gray-700 mb-1">Peso Suportado (kg)</label>
            <input type="number" 
                   step="0.01" 
                   name="peso_suportado" 
                   id="peso_suportado" 
                   value="{{ $carroceria->peso_suportado }}" 
                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('peso_suportado') border-red-500 @enderror" 
                   required>
            @error('peso_suportado')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" 
                    id="status" 
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
              <option value="1" {{ $carroceria->status ? 'selected' : '' }}>Ativa</option>
              <option value="0" {{ !$carroceria->status ? 'selected' : '' }}>Inativa</option>
            </select>
          </div>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end space-x-2 mt-6">
          <a href="{{ route('carrocerias.index') }}" 
             class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Cancelar
          </a>
          <button type="submit" 
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Atualizar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('custom-scripts')
<script>
// Máscara para chassi: só letras e números, até 17
const chassiInput = document.getElementById('chassi');
if (chassiInput) {
  chassiInput.addEventListener('input', function(e) {
    this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').substr(0, 17);
  });
}

// Máscara para placa: Mercosul (AAA0A00) ou antigo (AAA0000)
const placaInput = document.getElementById('placa');
if (placaInput) {
  placaInput.addEventListener('input', function(e) {
    let v = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    if (v.length > 7) v = v.substr(0, 7);
    this.value = v;
  });
}

// Máscara para peso: milhar e decimal
const pesoInput = document.getElementById('peso_suportado');
if (pesoInput) {
  pesoInput.addEventListener('input', function(e) {
    let v = this.value.replace(/[^0-9,\.]/g, '').replace(/(\..*)\./g, '$1');
    v = v.replace(/(,.*),/g, '$1');
    this.value = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
  });
}
</script>
@endpush
