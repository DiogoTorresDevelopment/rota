@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="flex flex-col">
  <!-- Cabeçalho com Título e Botão -->
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Entregas</h1>
      <p class="text-sm text-gray-600">Gerencie e cadastre entregas</p>
    </div>

    @if(hasPermission('deliveries.manage'))
    <a href="{{ route('deliveries.create') }}"
       class="flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 focus:ring-4 focus:ring-gray-300">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Adicionar
    </a>
    @endif
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

        <!-- Lado Direito - Pesquisa -->
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

      <!-- Tabela -->
      <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-gray-50 text-xs uppercase">
            <tr>
              <th class="px-6 py-3 text-gray-600 font-medium">CÓD.</th>
              <th class="px-6 py-3 text-gray-600 font-medium">ROTA</th>
              <th class="px-6 py-3 text-gray-600 font-medium">MOTORISTA</th>
              <th class="px-6 py-3 text-gray-600 font-medium">DATA ENVIO</th>
              <th class="px-6 py-3 text-gray-600 font-medium">DATA ENTREGA</th>
              <th class="px-6 py-3 text-gray-600 font-medium">STATUS</th>
              <th class="px-6 py-3 text-gray-600 font-medium text-right">AÇÕES</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($deliveries as $delivery)
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 text-gray-600">{{ $delivery->id }}</td>
              <td class="px-6 py-4 text-gray-900">{{ optional($delivery->route)->name ?? 'Rota Excluída' }}</td>
              <td class="px-6 py-4 text-gray-600">{{ optional($delivery->driver)->name ?? 'Motorista não encontrado' }}</td>
              <td class="px-6 py-4 text-gray-600">{{ $delivery->start_date?->format('d/m/Y') }}</td>
              <td class="px-6 py-4 text-gray-600">{{ $delivery->end_date?->format('d/m/Y') }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                  @switch($delivery->status)
                    @case('completed')
                      bg-green-100 text-green-800
                      @break
                    @case('cancelled')
                      bg-red-100 text-red-800
                      @break
                    @default
                      bg-blue-100 text-blue-800
                  @endswitch
                ">
                  @switch($delivery->status)
                    @case('completed')
                      Finalizada
                      @break
                    @case('cancelled')
                      Cancelada
                      @break
                    @default
                      Em andamento
                  @endswitch
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                @if($delivery->status === 'in_progress' && $delivery->route && hasPermission('deliveries.manage'))
                  <button type="button" 
                          onclick="completeDelivery({{ $delivery->id }})"
                          class="inline-flex items-center p-2 text-sm font-medium text-green-600 bg-green-100 rounded-lg hover:bg-green-200 focus:ring-4 focus:ring-green-300"
                          title="Finalizar entrega">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="sr-only">Finalizar entrega</span>
                  </button>
                @elseif(hasPermission('deliveries.view'))
                  <button type="button" 
                          onclick="viewDeliveryDetails({{ $delivery->id }})"
                          class="inline-flex items-center p-2 text-sm font-medium text-[#1a2438] bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-300"
                          title="Visualizar detalhes">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                      <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                    </svg>
                  </button>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-600">
          Mostrando {{ $deliveries->firstItem() ?? 0 }} até {{ $deliveries->lastItem() ?? 0 }} de {{ $deliveries->total() }} registros
        </div>
        {{ $deliveries->links() }}
      </div>
    </div>
  </div>
</div>


<!-- Modal de Detalhes da Entrega -->
<div id="delivery-details-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" style="z-index: 1000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Detalhes da Entrega</h3>
                    <button type="button" onclick="closeDeliveryDetailsModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Fechar</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Informações da Rota -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Informações da Rota</h4>
                        <div id="route-info" class="grid grid-cols-2 gap-4 text-sm">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>

                    <!-- Lista de Destinos -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Destinos</h4>
                        <div class="space-y-2" id="destinations-list">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" 
                                onclick="closeDeliveryDetailsModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            Fechar
                        </button>
                        <button type="button" 
                                onclick="reuseRoute()"
                                class="px-4 py-2 text-sm font-medium text-white bg-[#111928] border border-transparent rounded-md shadow-sm hover:bg-[#1a2438]">
                            Reutilizar Rota
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
<script>


// Atualizar as cores nos SweetAlert2
const sweetAlertConfig = {
    confirmButtonColor: '#111928',
    cancelButtonColor: '#d33',
};

async function completeDelivery(deliveryId) {
    const result = await Swal.fire({
        ...sweetAlertConfig,
        title: 'Confirmar finalização',
        text: "Deseja realmente finalizar esta entrega?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, finalizar!',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        // Mostrar loading
        Swal.fire({
            title: 'Processando...',
            html: 'Por favor, aguarde.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch(`/deliveries/${deliveryId}/complete`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: data.message || 'Entrega finalizada com sucesso!',
                confirmButtonColor: '#3085d6'
            });
            window.location.reload();
        } else {
            throw new Error(data.message || 'Erro ao finalizar entrega');
        }
    } catch (error) {
        console.error('Erro:', error);
        await Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao finalizar entrega: ' + error.message,
            confirmButtonColor: '#d33'
        });
    }
}

// Função para mostrar notificação de sucesso
function showSuccessNotification(message) {
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });

  Toast.fire({
    icon: 'success',
    title: message
  });
}

// Função para mostrar notificação de erro
function showErrorNotification(message) {
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
  });

  Toast.fire({
    icon: 'error',
    title: message
  });
}

let currentDeliveryId = null;

async function viewDeliveryDetails(deliveryId) {
    try {
        currentDeliveryId = deliveryId;
        
        // Mostrar loading
        Swal.fire({
            title: 'Carregando...',
            html: 'Buscando detalhes da entrega',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch(`/deliveries/${deliveryId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Preencher informações da rota
            document.getElementById('route-info').innerHTML = `
                <div>
                    <span class="font-medium">Nome da Rota:</span> ${data.data.route ? data.data.route.name : 'Rota Excluída'}
                </div>
                <div>
                    <span class="font-medium">Motorista:</span> ${data.data.driver ? data.data.driver.name : 'Motorista não encontrado'}
                </div>
                <div>
                    <span class="font-medium">Data Início:</span> ${data.data.start_date || 'N/A'}
                </div>
                <div>
                    <span class="font-medium">Data Fim:</span> ${data.data.end_date || 'N/A'}
                </div>
            `;

            // Preencher lista de destinos
            const destinationsList = document.getElementById('destinations-list');
            if (data.data.route && data.data.route.stops) {
                destinationsList.innerHTML = data.data.route.stops.map((stop, index) => `
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-[#1a2438]">
                            ${index + 1}
                        </div>
                        <div class="ml-4">
                            <h5 class="text-sm font-medium text-gray-900">${stop.name}</h5>
                            <p class="text-sm text-gray-500">${stop.street}, ${stop.number}</p>
                            <p class="text-sm text-gray-500">${stop.city} - ${stop.state}</p>
                        </div>
                    </div>
                `).join('');
            } else {
                destinationsList.innerHTML = '<p class="text-sm text-gray-500">Nenhum destino disponível</p>';
            }

            // Fechar loading e mostrar modal
            Swal.close();
            document.getElementById('delivery-details-modal').classList.remove('hidden');
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao carregar detalhes: ' + error.message
        });
    }
}

function closeDeliveryDetailsModal() {
    document.getElementById('delivery-details-modal').classList.add('hidden');
    currentDeliveryId = null;
}

async function reuseRoute() {
    const result = await Swal.fire({
        ...sweetAlertConfig,
        title: 'Confirmar reutilização',
        text: "Deseja iniciar uma nova entrega com esta rota?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, iniciar!',
        cancelButtonText: 'Cancelar'
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        // Mostrar loading
        Swal.fire({
            title: 'Processando...',
            html: 'Iniciando nova entrega',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch(`/deliveries/${currentDeliveryId}/reuse`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            await Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: data.message
            });
            window.location.reload();
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Erro ao reutilizar rota: ' + error.message
        });
    }
}
</script>
@endpush 