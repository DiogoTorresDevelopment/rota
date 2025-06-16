@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h6 class="card-title">Histórico da Entrega</h6>
                    
                    <!-- Informações da Rota -->
                    <div class="mb-4">
                        @if($delivery->deliveryRoute)
                            <h5>Rota: {{ $delivery->deliveryRoute->name }}</h5>
                            <p class="text-muted">{{ $delivery->deliveryRoute->description }}</p>
                            
                            <!-- Lista de Destinos -->
                            <div class="mt-3">
                                <h6>Destinos:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Ordem</th>
                                                <th>Nome</th>
                                                <th>Endereço</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($delivery->deliveryStops as $stop)
                                            <tr>
                                                <td>{{ $stop->order }}</td>
                                                <td>{{ $stop->deliveryRouteStop->name ?? '-' }}</td>
                                                <td>
                                                    @if($stop->deliveryRouteStop)
                                                        {{ $stop->deliveryRouteStop->street }}, {{ $stop->deliveryRouteStop->number }}
                                                        @if($stop->deliveryRouteStop->complement)
                                                            - {{ $stop->deliveryRouteStop->complement }}
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $stop->deliveryRouteStop->neighborhood }} - 
                                                            {{ $stop->deliveryRouteStop->city }}/{{ $stop->deliveryRouteStop->state }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">Endereço não disponível</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($stop->status === 'completed')
                                                        <span class="badge bg-success">Concluído</span>
                                                    @elseif($stop->status === 'cancelled')
                                                        <span class="badge bg-danger">Cancelado</span>
                                                    @else
                                                        <span class="badge bg-warning">Pendente</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Nenhum destino encontrado</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                Rota não encontrada para esta entrega
                            </div>
                        @endif
                    </div>

                    <!-- Histórico de Alterações -->
                    <h6 class="mt-4">Histórico de Alterações</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Parada</th>
                                    <th>Motorista</th>
                                    <th>Caminhão</th>
                                    <th>Carrocerias</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($delivery->histories as $history)
                                <tr>
                                    <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($history->deliveryStop && $history->deliveryStop->deliveryRouteStop)
                                            <div class="text-sm">
                                                <strong>{{ $history->deliveryStop->deliveryRouteStop->name }}</strong>
                                                <br>
                                                <span class="text-muted">
                                                    {{ $history->deliveryStop->deliveryRouteStop->street }}, 
                                                    {{ $history->deliveryStop->deliveryRouteStop->number }}
                                                    @if($history->deliveryStop->deliveryRouteStop->complement)
                                                        - {{ $history->deliveryStop->deliveryRouteStop->complement }}
                                                    @endif
                                                    <br>
                                                    {{ $history->deliveryStop->deliveryRouteStop->neighborhood }} - 
                                                    {{ $history->deliveryStop->deliveryRouteStop->city }}/{{ $history->deliveryStop->deliveryRouteStop->state }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted">Sem Alteração</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($history->driver)
                                            <div class="text-sm">
                                                <strong>{{ $history->driver->name }}</strong>
                                                <br>
                                                <span class="text-muted">CPF: {{ $history->driver->cpf }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">Sem Alteração</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($history->truck)
                                            <div class="text-sm">
                                                <strong>{{ $history->truck->marca }} {{ $history->truck->modelo }}</strong>
                                                <br>
                                                <span class="text-muted">Placa: {{ $history->truck->placa }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">Sem Alteração</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($history->carrocerias && $history->carrocerias->count() > 0)
                                            <div class="text-sm">
                                                @foreach($history->carrocerias as $carroceria)
                                                    <div>
                                                        <strong>{{ $carroceria->descricao }}</strong>
                                                        <br>
                                                        <span class="text-muted">Placa: {{ $carroceria->placa }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">Sem Alteração</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            @if($history->is_initial)
                                                <span class="badge bg-success">Início da Entrega</span>
                                            @endif
                                            @if($history->description)
                                                <br>
                                                <span class="text-muted">{{ $history->description }}</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum histórico encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-secondary">
                            Voltar para a Entrega
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

