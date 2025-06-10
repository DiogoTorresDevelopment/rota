@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">Editar Entrega</h6>
                    
                    <!-- Progress Steps -->
                    <div class="d-flex justify-content-between mb-4">
                        <div class="step {{ $currentStep == 1 ? 'active' : '' }}">
                            <div class="step-icon">
                                <i data-feather="truck"></i>
                            </div>
                            <div class="step-text">Dados da Entrega</div>
                        </div>
                        <div class="step-line"></div>
                        <div class="step {{ $currentStep == 2 ? 'active' : '' }}">
                            <div class="step-icon">
                                <i data-feather="map"></i>
                            </div>
                            <div class="step-text">Rota e Destinos</div>
                        </div>
                    </div>

                    <form id="edit-delivery-form" class="forms-sample">
                        @csrf
                        <input type="hidden" name="delivery_id" value="{{ $delivery->id }}">
                        <input type="hidden" name="current_step" value="{{ $currentStep }}">

                        <!-- Step 1: Dados da Entrega -->
                        <div class="step-content" id="step1" style="display: {{ $currentStep == 1 ? 'block' : 'none' }}">
                            <div class="row">
                                <!-- Motorista -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Motorista Atual</label>
                                        <div class="form-control bg-light mb-2">
                                            <strong>{{ $delivery->deliveryDriver->name }}</strong><br>
                                            CPF: {{ $delivery->deliveryDriver->cpf }}<br>
                                            Telefone: {{ $delivery->deliveryDriver->phone }}<br>
                                            Email: {{ $delivery->deliveryDriver->email }}
                                        </div>
                                        <label>Novo Motorista</label>
                                        <select name="driver_id" class="form-control">
                                            <option value="">Selecione um motorista</option>
                                            @foreach($availableDrivers as $driver)
                                                <option value="{{ $driver->id }}" {{ $delivery->original_driver_id == $driver->id ? 'selected' : '' }}>
                                                    {{ $driver->name }} - CPF: {{ $driver->cpf }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Caminhão -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Caminhão Atual</label>
                                        <div class="form-control bg-light mb-2">
                                            <strong>{{ $delivery->deliveryTruck->marca }} {{ $delivery->deliveryTruck->modelo }}</strong><br>
                                            Placa: {{ $delivery->deliveryTruck->placa }}<br>
                                            Chassi: {{ $delivery->deliveryTruck->chassi }}<br>
                                            Última Revisão: {{ $delivery->deliveryTruck->ultima_revisao ? $delivery->deliveryTruck->ultima_revisao->format('d/m/Y') : 'N/A' }}
                                        </div>
                                        <label>Novo Caminhão</label>
                                        <select name="truck_id" class="form-control">
                                            <option value="">Selecione um caminhão</option>
                                            @foreach($availableTrucks as $truck)
                                                <option value="{{ $truck->id }}" {{ $delivery->original_truck_id == $truck->id ? 'selected' : '' }}>
                                                    {{ $truck->marca }} {{ $truck->modelo }} - Placa: {{ $truck->placa }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Carrocerias -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Carrocerias Atuais</label>
                                        <div class="table-responsive mb-2">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Descrição</th>
                                                        <th>Placa</th>
                                                        <th>Chassi</th>
                                                        <th>Peso Suportado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($delivery->deliveryCarrocerias as $carroceria)
                                                    <tr>
                                                        <td>{{ $carroceria->descricao }}</td>
                                                        <td>{{ $carroceria->placa }}</td>
                                                        <td>{{ $carroceria->chassi }}</td>
                                                        <td>{{ $carroceria->peso_suportado }} kg</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <label>Novas Carrocerias</label>
                                        <select name="carroceria_ids[]" class="form-control select2" multiple>
                                            @foreach($availableCarrocerias as $carroceria)
                                                <option value="{{ $carroceria->id }}" {{ in_array($carroceria->id, $delivery->deliveryCarrocerias->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                    {{ $carroceria->descricao }} - Placa: {{ $carroceria->placa }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Data de Início -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Data de Início Atual</label>
                                        <div class="form-control bg-light mb-2">
                                            {{ $delivery->start_date ? $delivery->start_date->format('d/m/Y H:i') : 'N/A' }}
                                        </div>
                                        <label>Nova Data de Início</label>
                                        <input type="datetime-local" name="start_date" class="form-control" value="{{ $delivery->start_date ? $delivery->start_date->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>

                                <!-- Data de Término -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Data de Término Atual</label>
                                        <div class="form-control bg-light mb-2">
                                            {{ $delivery->end_date ? $delivery->end_date->format('d/m/Y H:i') : 'N/A' }}
                                        </div>
                                        <label>Nova Data de Término</label>
                                        <input type="datetime-local" name="end_date" class="form-control" value="{{ $delivery->end_date ? $delivery->end_date->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('deliveries.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">Cancelar</a>
                                <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="nextStep(2)">Próximo</button>
                            </div>
                        </div>

                        <!-- Step 2: Rota e Destinos -->
                        <div class="step-content" id="step2" style="display: {{ $currentStep == 2 ? 'block' : 'none' }}">
                            <div class="row">
                                <!-- Rota -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Rota Atual</label>
                                        <div class="form-control bg-light mb-2">
                                            <strong>{{ $delivery->deliveryRoute->name }}</strong><br>
                                            {{ $delivery->deliveryRoute->description }}
                                        </div>
                                        <label>Nova Rota</label>
                                        <select name="route_id" class="form-control">
                                            <option value="">Selecione uma rota</option>
                                            @foreach($availableRoutes as $route)
                                                <option value="{{ $route->id }}" {{ $delivery->original_route_id == $route->id ? 'selected' : '' }}>
                                                    {{ $route->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Paradas da Rota -->
                                <div class="col-md-12 mt-4">
                                    <h6 class="card-title">Paradas da Rota Atual</h6>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Ordem</th>
                                                    <th>Nome</th>
                                                    <th>Endereço</th>
                                                    <th>Status</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($delivery->deliveryStops as $stop)
                                                <tr>
                                                    <td>{{ $stop->order }}</td>
                                                    <td>{{ $stop->deliveryRouteStop->name }}</td>
                                                    <td>
                                                        {{ $stop->deliveryRouteStop->street }}, {{ $stop->deliveryRouteStop->number }}
                                                        {{ $stop->deliveryRouteStop->complement ? '- ' . $stop->deliveryRouteStop->complement : '' }}
                                                        <br>
                                                        {{ $stop->deliveryRouteStop->neighborhood }} - {{ $stop->deliveryRouteStop->city }}/{{ $stop->deliveryRouteStop->state }}
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $stop->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                                            {{ $stop->status === 'completed' ? 'Concluída' : 'Pendente' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('deliveries.edit-stop', ['delivery' => $delivery->id, 'stop' => $stop->id]) }}" 
                                                           class="px-3 py-1 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                            Editar
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2" onclick="prevStep(1)">Anterior</button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Salvar Alterações</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa Select2
    $('.select2').select2();

    // Função para navegar entre as etapas
    window.nextStep = function(step) {
        document.querySelector('input[name="current_step"]').value = step;
        document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');
        document.getElementById('step' + step).style.display = 'block';
        
        // Atualizar indicadores de progresso
        document.querySelectorAll('.step').forEach((el, index) => {
            if (index + 1 <= step) {
                el.classList.add('active');
            } else {
                el.classList.remove('active');
            }
        });
    };

    window.prevStep = function(step) {
        nextStep(step);
    };

    // Manipula o envio do formulário
    document.getElementById('edit-delivery-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Envia os dados via AJAX
        fetch(`/deliveries/${formData.get('delivery_id')}/change-resources`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Sucesso!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/deliveries';
                    }
                });
            } else {
                Swal.fire({
                    title: 'Erro!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Erro!',
                text: 'Ocorreu um erro ao salvar as alterações.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });
});
</script>

<style>
.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    flex: 1;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.step.active .step-icon {
    background-color: #4e73df;
    color: white;
}

.step-text {
    font-size: 14px;
    color: #6c757d;
}

.step.active .step-text {
    color: #4e73df;
    font-weight: 600;
}

.step-line {
    flex: 1;
    height: 2px;
    background-color: #e9ecef;
    margin: 0 10px;
    margin-top: 20px;
}
</style>
@endpush 