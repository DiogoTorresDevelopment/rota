@extends('layout.master')

@section('content')
<div class="h-full flex flex-col">
  <div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('carrocerias.store') }}" method="POST">
      @csrf
      <div class="mb-4">
        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
        <input type="text" name="descricao" id="descricao" class="w-full h-12 px-4 rounded-lg border-gray-300" required placeholder="Ex: Baú Frigorífico">
      </div>
      <div class="mb-4">
        <label for="chassi" class="block text-sm font-medium text-gray-700 mb-2">Chassi</label>
        <input type="text" name="chassi" id="chassi" maxlength="17" class="w-full h-12 px-4 rounded-lg border-gray-300" required pattern="[A-Za-z0-9]{1,17}" placeholder="Ex: 9BWZZZ377VT004251">
      </div>
      <div class="mb-4">
        <label for="placa" class="block text-sm font-medium text-gray-700 mb-2">Placa</label>
        <input type="text" name="placa" id="placa" maxlength="7" class="w-full h-12 px-4 rounded-lg border-gray-300 uppercase" required pattern="[A-Z]{3}[0-9][A-Z0-9][0-9]{2}" placeholder="AAA0A00 ou AAA0000">
      </div>
      <div class="mb-4">
        <label for="peso_suportado" class="block text-sm font-medium text-gray-700 mb-2">Peso Suportado (kg)</label>
        <input type="text" name="peso_suportado" id="peso_suportado" class="w-full h-12 px-4 rounded-lg border-gray-300" required placeholder="Ex: 12000">
      </div>
      <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
        <select name="status" id="status" class="w-full h-12 px-4 rounded-lg border-gray-300">
          <option value="1">Ativa</option>
          <option value="0">Inativa</option>
        </select>
      </div>
      <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg">Salvar</button>
    </form>
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
