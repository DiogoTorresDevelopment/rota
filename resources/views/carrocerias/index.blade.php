@extends('layout.master')

@section('content')
<div class="h-full flex flex-col">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Carrocerias</h1>
    </div>
    <a href="{{ route('carrocerias.create') }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg">Adicionar</a>
  </div>

  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-xs uppercase">
          <tr>
            <th class="px-6 py-3">Descrição</th>
            <th class="px-6 py-3">Chassi</th>
            <th class="px-6 py-3">Placa</th>
            <th class="px-6 py-3">Peso Suportado</th>
            <th class="px-6 py-3">Status</th>
            <th class="px-6 py-3 text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($carrocerias as $carroceria)
          <tr>
            <td class="px-6 py-4">{{ $carroceria->descricao }}</td>
            <td class="px-6 py-4">{{ $carroceria->chassi }}</td>
            <td class="px-6 py-4">{{ $carroceria->placa }}</td>
            <td class="px-6 py-4">{{ $carroceria->peso_suportado }}</td>
            <td class="px-6 py-4">{{ $carroceria->status ? 'Ativa' : 'Inativa' }}</td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('carrocerias.edit', $carroceria) }}" class="text-blue-600">Editar</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
