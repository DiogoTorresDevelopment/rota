@extends('layout.master')

@section('content')
<div class="h-full flex flex-col">
  <div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('carrocerias.update', $carroceria) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-4">
        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
        <input type="text" name="descricao" id="descricao" value="{{ $carroceria->descricao }}" class="w-full h-12 px-4 rounded-lg border-gray-300" required>
      </div>
      <div class="mb-4">
        <label for="chassi" class="block text-sm font-medium text-gray-700 mb-2">Chassi</label>
        <input type="text" name="chassi" id="chassi" value="{{ $carroceria->chassi }}" class="w-full h-12 px-4 rounded-lg border-gray-300" required>
      </div>
      <div class="mb-4">
        <label for="placa" class="block text-sm font-medium text-gray-700 mb-2">Placa</label>
        <input type="text" name="placa" id="placa" value="{{ $carroceria->placa }}" class="w-full h-12 px-4 rounded-lg border-gray-300 uppercase" required>
      </div>
      <div class="mb-4">
        <label for="peso_suportado" class="block text-sm font-medium text-gray-700 mb-2">Peso Suportado (kg)</label>
        <input type="number" step="0.01" name="peso_suportado" id="peso_suportado" value="{{ $carroceria->peso_suportado }}" class="w-full h-12 px-4 rounded-lg border-gray-300" required>
      </div>
      <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
        <select name="status" id="status" class="w-full h-12 px-4 rounded-lg border-gray-300">
          <option value="1" {{ $carroceria->status ? 'selected' : '' }}>Ativa</option>
          <option value="0" {{ !$carroceria->status ? 'selected' : '' }}>Inativa</option>
        </select>
      </div>
      <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg">Salvar</button>
    </form>
  </div>
</div>
@endsection
