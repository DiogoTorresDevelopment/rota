@extends('layout.master')

@section('content')
<div class="h-full flex flex-col">
  <div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('carrocerias.store') }}" method="POST">
      @csrf
      <div class="mb-4">
        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
        <input type="text" name="descricao" id="descricao" class="w-full h-12 px-4 rounded-lg border-gray-300" required>
      </div>
      <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg">Salvar</button>
    </form>
  </div>
</div>
@endsection
