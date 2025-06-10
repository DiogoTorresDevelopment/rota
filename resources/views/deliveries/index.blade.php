@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')

<style>
  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    border: none !important;;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice__display{
    padding-left: 8px !important;
  }
</style>

<div class="flex flex-col">
  <!-- Cabeçalho com Título e Botão -->
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Entregas</h1>
      <p class="text-sm text-gray-600">Gerencie e cadastre entregas</p>
    </div>

    @if(hasPermission('deliveries.manage'))
    <button onclick="openCreateDeliveryModal()"
       class="flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 focus:ring-4 focus:ring-gray-300">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Adicionar
    </button>
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
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                ID
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Rota
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Motorista
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Caminhão
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Data Início
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Data Fim
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ações
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($deliveries as $delivery)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ $delivery->id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ $delivery->deliveryRoute->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ $delivery->deliveryDriver->name }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ $delivery->deliveryTruck->marca }} {{ $delivery->deliveryTruck->modelo }} - {{ $delivery->deliveryTruck->placa }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    @if($delivery->status === 'in_progress') bg-yellow-100 text-yellow-800
                    @elseif($delivery->status === 'completed') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ $delivery->status === 'in_progress' ? 'Em Andamento' : 
                       ($delivery->status === 'completed' ? 'Concluída' : 'Cancelada') }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ $delivery->start_date ? $delivery->start_date->format('d/m/Y H:i') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ $delivery->end_date ? $delivery->end_date->format('d/m/Y H:i') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <a href="{{ route('deliveries.show', $delivery->id) }}" 
                       class="text-blue-600 hover:text-blue-900">
                       <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('deliveries.edit', $delivery->id) }}" 
                       class="text-yellow-600 hover:text-yellow-900">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </a>
                    @if($delivery->status === 'in_progress')
                      <button onclick="completeDelivery({{ $delivery->id }})" 
                              class="text-green-600 hover:text-green-900">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                      </button>
                    @endif
                  </div>
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
                    <!-- Informações da Entrega -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Informações da Entrega</h4>
                        <div id="delivery-info" class="grid grid-cols-2 gap-4 text-sm">
                            <!-- Será preenchido via JavaScript -->
                        </div>
                    </div>

                    <!-- Lista de Destinos -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Destinos</h4>
                        <div class="space-y-2" id="delivery-stops">
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
                                onclick="completeDelivery()"
                                id="complete-delivery-btn"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 hidden">
                            Finalizar Entrega
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Criar Entrega -->
<div id="create-delivery-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" style="z-index: 1000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Nova Entrega</h3>
                    <button type="button" onclick="closeCreateDeliveryModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Fechar</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="create-delivery-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="route_id" class="block text-sm font-medium text-gray-700">Rota</label>
                        <select id="route_id" name="route_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Selecione uma rota</option>
                            @foreach($availableRoutes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="driver_id" class="block text-sm font-medium text-gray-700">Motorista</label>
                        <select id="driver_id" name="driver_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Selecione um motorista</option>
                            @foreach($availableDrivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="truck_id" class="block text-sm font-medium text-gray-700">Caminhão</label>
                        <select id="truck_id" name="truck_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Selecione um caminhão</option>
                            @foreach($availableTrucks as $truck)
                                <option value="{{ $truck->id }}">{{ $truck->marca }} {{ $truck->modelo }} - {{ $truck->placa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="carroceria_ids" class="block text-sm font-medium text-gray-700">Carrocerias</label>
                        <select id="carroceria_ids" name="carroceria_ids[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @foreach($availableCarrocerias as $carroceria)
                                <option value="{{ $carroceria->id }}">{{ $carroceria->descricao }} - {{ $carroceria->placa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Envio</label>
                            <input type="datetime-local" 
                                   id="start_date" 
                                   name="start_date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   required>
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">Data de Entrega</label>
                            <input type="datetime-local" 
                                   id="end_date" 
                                   name="end_date" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" 
                                onclick="closeCreateDeliveryModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-[#111928] border border-transparent rounded-md shadow-sm hover:bg-[#1a2438]">
                            Criar Entrega
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Adicionar o Modal de Edição -->
<div id="edit-delivery-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" style="z-index: 1000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Editar Entrega</h3>
                    <button type="button" onclick="closeEditDeliveryModal()" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Fechar</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="edit-delivery-form" class="space-y-6">
                    <input type="hidden" id="edit-delivery-id" name="delivery_id">
                    
                    <!-- Rota -->
                    <div>
                        <label for="edit-route" class="block text-sm font-medium text-gray-700">Rota</label>
                        <select id="edit-route" name="route_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                            <option value="">Selecione uma rota</option>
                            @foreach($availableRoutes as $route)
                                <option value="{{ $route->id }}">{{ $route->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Motorista -->
                    <div>
                        <label for="edit-driver" class="block text-sm font-medium text-gray-700">Motorista</label>
                        <select id="edit-driver" name="driver_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                            <option value="">Selecione um motorista</option>
                            @foreach($availableDrivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Caminhão -->
                    <div>
                        <label for="edit-truck" class="block text-sm font-medium text-gray-700">Caminhão</label>
                        <select id="edit-truck" name="truck_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md" required>
                            <option value="">Selecione um caminhão</option>
                            @foreach($availableTrucks as $truck)
                                <option value="{{ $truck->id }}">{{ $truck->marca }} {{ $truck->modelo }} - {{ $truck->placa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Carrocerias -->
                    <div>
                        <label for="edit-carrocerias" class="block text-sm font-medium text-gray-700">Carrocerias</label>
                        <select id="edit-carrocerias" name="carroceria_ids[]" class="select2-carrocerias mt-1 block w-full" multiple="multiple" required>
                            @foreach($availableCarrocerias as $carroceria)
                                <option value="{{ $carroceria->id }}">{{ $carroceria->descricao }} - {{ $carroceria->placa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Datas -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="edit-start-date" class="block text-sm font-medium text-gray-700">Data de Envio</label>
                            <input type="datetime-local" id="edit-start-date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="edit-end-date" class="block text-sm font-medium text-gray-700">Data de Entrega</label>
                            <input type="datetime-local" id="edit-end-date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="closeEditDeliveryModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
<script>
// Configuração do SweetAlert2
const sweetAlertConfig = {
    confirmButtonColor: '#1a2438',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Sim',
    cancelButtonText: 'Cancelar',
    showClass: {
        popup: 'animate__animated animate__fadeInDown'
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp'
    }
};

let currentDeliveryId = null;

// Funções globais para o modal de criação
function openCreateDeliveryModal() {
    document.getElementById('create-delivery-modal').classList.remove('hidden');
}

function closeCreateDeliveryModal() {
    document.getElementById('create-delivery-modal').classList.add('hidden');
    document.getElementById('create-delivery-form').reset();
    $('#carroceria_ids').val(null).trigger('change');
}

// Funções de visualização e edição
async function viewDeliveryDetails(deliveryId) {
    currentDeliveryId = deliveryId;
    
    try {
        const response = await fetch(`/deliveries/${deliveryId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            const delivery = data.data;
            
            // Preencher informações da entrega
            document.getElementById('delivery-info').innerHTML = `
                <div>
                    <p class="text-gray-600">Rota: <span class="text-gray-900">${delivery.route.name}</span></p>
                    <p class="text-gray-600">Motorista: <span class="text-gray-900">${delivery.driver?.name || 'Não atribuído'}</span></p>
                    <p class="text-gray-600">Caminhão: <span class="text-gray-900">${delivery.truck ? `${delivery.truck.marca} ${delivery.truck.modelo} - ${delivery.truck.placa}` : 'Não atribuído'}</span></p>
                </div>
                <div>
                    <p class="text-gray-600">Data de Envio: <span class="text-gray-900">${delivery.start_date || 'Não definida'}</span></p>
                    <p class="text-gray-600">Data de Entrega: <span class="text-gray-900">${delivery.end_date || 'Não definida'}</span></p>
                    <p class="text-gray-600">Status: <span class="text-gray-900">${getStatusText(delivery.status)}</span></p>
                </div>
            `;

            // Preencher lista de destinos
            const stopsHtml = delivery.route.stops.map((stop, index) => `
                <div class="flex items-center justify-between p-3 bg-white border rounded-lg ${stop.order === delivery.current_stop?.order ? 'border-blue-500' : ''}">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100">
                            ${index + 1}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">${stop.name}</p>
                            <p class="text-sm text-gray-500">${stop.street}, ${stop.number} - ${stop.city}/${stop.state}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        ${delivery.status === 'in_progress' && stop.order === delivery.current_stop?.order ? `
                            <button onclick="completeStop(${delivery.id})" 
                                    class="px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                Concluir
                            </button>
                        ` : ''}
                        ${stop.order < delivery.current_stop?.order ? `
                            <span class="px-2 py-1 text-xs font-medium text-green-800 bg-green-100 rounded-full">
                                Concluído
                            </span>
                        ` : ''}
                    </div>
                </div>
            `).join('');

            document.getElementById('delivery-stops').innerHTML = stopsHtml;

            // Mostrar/esconder botão de finalizar entrega
            const completeBtn = document.getElementById('complete-delivery-btn');
            if (delivery.status === 'in_progress') {
                completeBtn.classList.remove('hidden');
            } else {
                completeBtn.classList.add('hidden');
            }

            // Mostrar modal
            document.getElementById('delivery-details-modal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Erro ao carregar detalhes:', error);
        Swal.fire({
            ...sweetAlertConfig,
            title: 'Erro!',
            text: 'Erro ao carregar detalhes da entrega.',
            icon: 'error'
        });
    }
}

function closeDeliveryDetailsModal() {
    document.getElementById('delivery-details-modal').classList.add('hidden');
    currentDeliveryId = null;
}

function getStatusText(status) {
    switch (status) {
        case 'in_progress':
            return 'Em andamento';
        case 'completed':
            return 'Finalizada';
        case 'cancelled':
            return 'Cancelada';
        default:
            return status;
    }
}

async function completeStop(deliveryId) {
    try {
        const confirmResult = await Swal.fire({
            ...sweetAlertConfig,
            title: 'Confirmar conclusão',
            text: "Deseja realmente marcar esta parada como concluída?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, concluir!',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmResult.isConfirmed) {
            return;
        }

        // Mostrar loading
        Swal.fire({
            title: 'Processando...',
            html: 'Por favor, aguarde.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch(`/deliveries/${deliveryId}/complete-stop`, {
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
            Swal.fire({
                ...sweetAlertConfig,
                title: 'Sucesso!',
                text: data.message,
                icon: 'success'
            }).then(() => {
                // Recarregar detalhes da entrega
                viewDeliveryDetails(deliveryId);
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        Swal.fire({
            ...sweetAlertConfig,
            title: 'Erro!',
            text: error.message || 'Erro ao concluir parada.',
            icon: 'error'
        });
    }
}

async function completeDelivery(deliveryId) {
    try {
        const confirmResult = await Swal.fire({
            ...sweetAlertConfig,
            title: 'Confirmar finalização',
            text: "Deseja realmente finalizar esta entrega?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, finalizar!',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmResult.isConfirmed) {
            return;
        }

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
            Swal.fire({
                ...sweetAlertConfig,
                title: 'Sucesso!',
                text: data.message,
                icon: 'success'
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        Swal.fire({
            ...sweetAlertConfig,
            title: 'Erro!',
            text: error.message || 'Erro ao finalizar entrega.',
            icon: 'error'
        });
    }
}

async function editDelivery(deliveryId) {
    try {
        const response = await fetch(`/deliveries/${deliveryId}/details`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            const delivery = data.data;
            
            // Preencher o formulário com os dados da entrega
            document.getElementById('edit-delivery-id').value = delivery.id;
            document.getElementById('edit-route').value = delivery.route.id;
            document.getElementById('edit-driver').value = delivery.driver?.id || '';
            document.getElementById('edit-truck').value = delivery.truck?.id || '';

            // Preencher carrocerias
            const carroceriasSelect = $('#edit-carrocerias');
            carroceriasSelect.val(delivery.carrocerias.map(c => c.id));
            carroceriasSelect.trigger('change');

            // Preencher datas
            const startDate = delivery.start_date ? new Date(delivery.start_date.split(' ').join('T')).toISOString().slice(0, 16) : '';
            const endDate = delivery.end_date ? new Date(delivery.end_date.split(' ').join('T')).toISOString().slice(0, 16) : '';
            
            document.getElementById('edit-start-date').value = startDate;
            document.getElementById('edit-end-date').value = endDate;

            // Mostrar modal
            document.getElementById('edit-delivery-modal').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Erro ao carregar detalhes:', error);
        Swal.fire({
            ...sweetAlertConfig,
            title: 'Erro!',
            text: 'Erro ao carregar detalhes da entrega.',
            icon: 'error'
        });
    }
}

function closeEditDeliveryModal() {
    document.getElementById('edit-delivery-modal').classList.add('hidden');
    document.getElementById('edit-delivery-form').reset();
    $('#edit-carrocerias').val(null).trigger('change');
}

// Inicializar Select2 e formulário de edição
document.addEventListener('DOMContentLoaded', function() {
    $('.select2-carrocerias').select2({
        width: '100%',
        placeholder: 'Selecione as carrocerias',
        allowClear: true
    });

    document.getElementById('edit-delivery-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const deliveryId = document.getElementById('edit-delivery-id').value;
        const formData = new FormData(this);
        const data = {
            route_id: formData.get('route_id'),
            driver_id: formData.get('driver_id'),
            truck_id: formData.get('truck_id'),
            carroceria_ids: $('#edit-carrocerias').val(),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date')
        };

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

            const response = await fetch(`/deliveries/${deliveryId}/change-resources`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    ...sweetAlertConfig,
                    title: 'Sucesso!',
                    text: result.message,
                    icon: 'success'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            Swal.fire({
                ...sweetAlertConfig,
                title: 'Erro!',
                text: error.message || 'Erro ao atualizar entrega.',
                icon: 'error'
            });
        }
    });

    // Adicionar evento de click para todos os botões de visualizar
    document.querySelectorAll('[data-action="view-delivery"]').forEach(button => {
        button.addEventListener('click', function() {
            const deliveryId = this.getAttribute('data-delivery-id');
            viewDeliveryDetails(deliveryId);
        });
    });

    // Inicializar Select2 para carrocerias no formulário de criação
    $('#carroceria_ids').select2({
        width: '100%',
        placeholder: 'Selecione as carrocerias',
        allowClear: true
    });

    // Adicionar evento de submit para o formulário de criação
    document.getElementById('create-delivery-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = {
            route_id: formData.get('route_id'),
            driver_id: formData.get('driver_id'),
            truck_id: formData.get('truck_id'),
            carroceria_ids: $('#carroceria_ids').val(),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date')
        };

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

            const response = await fetch('/deliveries', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    ...sweetAlertConfig,
                    title: 'Sucesso!',
                    text: result.message,
                    icon: 'success'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            Swal.fire({
                ...sweetAlertConfig,
                title: 'Erro!',
                text: error.message || 'Erro ao criar entrega.',
                icon: 'error'
            });
        }
    });
});
</script>
@endpush 