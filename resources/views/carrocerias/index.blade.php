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
      <div class="relative overflow-y-auto overflow-x-hidden custom-scrollbar">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-50 text-xs uppercase sticky top-0">
            <tr>
              <th class="px-6 py-3 text-gray-600 font-medium">Descrição</th>
              <th class="px-6 py-3 text-gray-600 font-medium">Chassi</th>
              <th class="px-6 py-3 text-gray-600 font-medium">Placa</th>
              <th class="px-6 py-3 text-gray-600 font-medium">Peso Suportado</th>
              <th class="px-6 py-3 text-gray-600 font-medium">Status</th>
              <th class="px-6 py-3 text-gray-600 font-medium text-right">Ações</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($carrocerias as $carroceria)
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 text-gray-900">{{ $carroceria->descricao }}</td>
              <td class="px-6 py-4 text-gray-600">{{ $carroceria->chassi }}</td>
              <td class="px-6 py-4 text-gray-600">{{ $carroceria->placa }}</td>
              <td class="px-6 py-4 text-gray-600">{{ $carroceria->peso_suportado }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $carroceria->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                  {{ $carroceria->status ? 'Ativa' : 'Inativa' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right relative">
                <button class="text-gray-400 hover:text-gray-600" data-dropdown-toggle="dropdown-{{ $carroceria->id }}">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 14c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-7c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                  </svg>
                </button>
                <div id="dropdown-{{ $carroceria->id }}" class="hidden absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                  <ul class="py-1">
                    <li>
                      <a href="{{ route('carrocerias.edit', $carroceria) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Editar
                      </a>
                    </li>
                    <li>
                      <a href="{{ route('carrocerias.show', $carroceria) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Visualizar
                      </a>
                    </li>
                    <li>
                      <form action="{{ route('carrocerias.destroy', $carroceria) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-50 delete-carroceria">
                          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                          </svg>
                          Excluir
                        </button>
                      </form>
                    </li>
                  </ul>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown actions
    const dropdowns = document.querySelectorAll('[data-dropdown-toggle]');
    function closeAllDropdowns(exceptId = null) {
        dropdowns.forEach(dropdown => {
            const dropdownId = dropdown.getAttribute('data-dropdown-toggle');
            if (dropdownId !== exceptId) {
                const dropdownMenu = document.getElementById(dropdownId);
                if (dropdownMenu) {
                    dropdownMenu.classList.add('hidden');
                }
            }
        });
    }
    dropdowns.forEach(dropdown => {
        const dropdownId = dropdown.getAttribute('data-dropdown-toggle');
        const dropdownMenu = document.getElementById(dropdownId);
        if (dropdownMenu) {
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeAllDropdowns(dropdownId);
                dropdownMenu.classList.toggle('hidden');
            });
        }
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown-toggle]')) {
            closeAllDropdowns();
        }
    });
    // Confirmação de exclusão com SweetAlert2
    const deleteButtons = document.querySelectorAll('.delete-carroceria');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Tem certeza?',
                text: "Esta ação não poderá ser revertida!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(form.action, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: 'Carroceria excluída com sucesso!',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Erro ao excluir a carroceria');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Erro!',
                            text: error.message,
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    });
                }
            });
        });
    });
});
</script>
@endpush
