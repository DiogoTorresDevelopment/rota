@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@push('style')
<style>
/* Estilização da scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endpush

@section('content')
<div class="flex flex-col">
  <!-- Cabeçalho com Título e Botão -->
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Motoristas</h1>
      <p class="text-sm text-gray-600">Cadastrar e gerenciar motoristas</p>
    </div>

    <a href="{{ route('drivers.create') }}" 
       class="flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 focus:ring-4 focus:ring-gray-300">
      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Adicionar
    </a>
  </div>

  <!-- Card principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <!-- Filtros -->
      <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
        <!-- Lado Esquerdo - Filtros -->
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">Exibir</span>
            <select class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="200">200</option>
            </select>
            <span class="text-sm text-gray-600">elementos.</span>
          </div>
        </div>

        <!-- Lado Direito - Apenas Pesquisa -->
        <div class="relative">
          <input type="text" 
                 placeholder="Buscar registros..." 
                 class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm w-64">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Container da tabela -->
      <div class="relative overflow-y-auto overflow-x-hidden custom-scrollbar">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-50 text-xs uppercase sticky top-0">
            <tr>
              <th class="px-6 py-3 text-gray-600 font-medium">CÓD.</th>
              <th class="px-6 py-3 text-gray-600 font-medium">NOME</th>
              <th class="px-6 py-3 text-gray-600 font-medium">CPF</th>
              <th class="px-6 py-3 text-gray-600 font-medium">TELEFONE</th>
              <th class="px-6 py-3 text-gray-600 font-medium">EMAIL</th>
              <th class="px-6 py-3 text-gray-600 font-medium">STATUS</th>
              <th class="px-6 py-3 text-gray-600 font-medium text-right">AÇÕES</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($drivers as $driver)
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 text-gray-600">{{ $driver->id }}</td>
              <td class="px-6 py-4 text-gray-900">{{ $driver->name }}</td>
              <td class="px-6 py-4 text-gray-600">{{ $driver->cpf }}</td>
              <td class="px-6 py-4 text-gray-600">{{ $driver->phone }}</td>
              <td class="px-6 py-4 text-gray-600">{{ $driver->email }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $driver->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                  {{ $driver->status ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <button class="text-gray-400 hover:text-gray-600" data-dropdown-toggle="dropdown-{{ $driver->id }}">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 3c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 14c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-7c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                  </svg>
                </button>
                <div id="dropdown-{{ $driver->id }}" class="hidden absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                  <ul class="py-1">
                    <li>
                      <a href="{{ route('drivers.edit', $driver->id) }}" 
                          class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                        Editar
                      </a>
                    </li>
                    <li>
                      <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-50 delete-driver">
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

      <!-- Footer com Paginação -->
      <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
        <div>
          Mostrando 1 até {{ count($drivers) }} de {{ count($drivers) }} registros
        </div>
        <div class="flex items-center gap-2">
          <button class="px-2 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>
            Anterior
          </button>
          <span class="px-3 py-1 bg-gray-900 text-white rounded-lg">1</span>
          <button class="px-2 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50" disabled>
            Próximo
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/datatables-net/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.js') }}"></script>
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa os dropdowns
    const dropdowns = document.querySelectorAll('[data-dropdown-toggle]');
    
    // Função para fechar todos os dropdowns exceto o atual
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

    // Adiciona evento de clique para cada botão de dropdown
    dropdowns.forEach(dropdown => {
        const dropdownId = dropdown.getAttribute('data-dropdown-toggle');
        const dropdownMenu = document.getElementById(dropdownId);
        
        if (dropdownMenu) {
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Fecha todos os outros dropdowns
                closeAllDropdowns(dropdownId);
                
                // Toggle do dropdown atual
                dropdownMenu.classList.toggle('hidden');
            });
        }
    });

    // Fecha todos os dropdowns quando clicar fora
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown-toggle]')) {
            closeAllDropdowns();
        }
    });

    // Confirmação de exclusão com SweetAlert2
    const deleteButtons = document.querySelectorAll('.delete-driver');
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
                    // Fazer requisição AJAX ao invés de submit normal
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
                            // Mostra mensagem de sucesso
                            Swal.fire({
                                title: 'Sucesso!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then(() => {
                                // Recarrega a página após a confirmação
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Erro ao excluir o motorista');
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

    // Auto-hide para alertas
    setTimeout(function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            alert.remove();
        });
    }, 5000);
});
</script>
@endpush 