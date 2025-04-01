@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
  <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" />
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
  <div>
    <h4 class="mb-3 mb-md-0">Dashboard</h4>
  </div>
</div>

<!-- Cards -->
<div class="row">
  <!-- Card Rotas -->
  <div class="col-md-3">
    <div class="card text-white" style="background-color: #1a2438">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i data-feather="map" class="icon-lg mb-0 me-2"></i>
          <div>
            <h6 class="card-title mb-0 text-white">Total de Rotas</h6>
            <h3 class="mb-2 text-white">{{ $totalRoutes }}</h3>
            <p class="text-white-50 mb-0">Rotas cadastradas</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Entregas -->
  <div class="col-md-3">
    <div class="card text-white bg-success">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i data-feather="truck" class="icon-lg mb-0 me-2"></i>
          <div>
            <h6 class="card-title mb-0 text-white">Entregas Ativas</h6>
            <h3 class="mb-2 text-white">{{ $activeDeliveries }}</h3>
            <p class="text-white-50 mb-0">Em andamento</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Motoristas -->
  <div class="col-md-3">
    <div class="card text-white bg-info">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i data-feather="users" class="icon-lg mb-0 me-2"></i>
          <div>
            <h6 class="card-title mb-0 text-white">Motoristas</h6>
            <h3 class="mb-2 text-white">{{ $activeDrivers }}</h3>
            <p class="text-white-50 mb-0">Motoristas ativos</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Caminhões -->
  <div class="col-md-3">
    <div class="card text-white bg-warning">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <i data-feather="truck" class="icon-lg mb-0 me-2"></i>
          <div>
            <h6 class="card-title mb-0 text-white">Caminhões</h6>
            <h3 class="mb-2 text-white">{{ $totalTrucks }}</h3>
            <p class="text-white-50 mb-0">Frota total</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Mapa e Lista -->
<div class="row mt-3">
  <!-- Mapa -->
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Mapa de Rotas Ativas</h6>
        <div id="map" style="height: 400px;"></div>
      </div>
    </div>
  </div>

  <!-- Lista de Rotas -->
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Rotas em Andamento</h6>
        <div class="list-group">
          @forelse($activeRoutes as $route)
            <a href="#" class="list-group-item list-group-item-action" onclick="highlightRoute({{ json_encode($route->stops) }})">
              <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1">{{ $route->name }}</h6>
                <small>{{ $route->deliveries->first()?->start_date?->diffForHumans() }}</small>
              </div>
              <p class="mb-1">Motorista: {{ $route->driver->name }}</p>
              <small>{{ count($route->stops) }} paradas</small>
            </a>
          @empty
            <div class="text-center p-3">
              <p class="text-muted mb-0">Nenhuma rota em andamento</p>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabela de Entregas -->
<div class="row mt-3">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Últimas Entregas</h6>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>ROTA</th>
                <th>MOTORISTA</th>
                <th>INÍCIO</th>
                <th>FIM</th>
                <th>STATUS</th>
                <th>PROGRESSO</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentDeliveries as $delivery)
                <tr>
                  <td>{{ $delivery->id }}</td>
                  <td>{{ $delivery->route->name }}</td>
                  <td>{{ $delivery->route->driver->name }}</td>
                  <td>{{ $delivery->start_date?->format('d/m/Y H:i') }}</td>
                  <td>{{ $delivery->end_date?->format('d/m/Y H:i') }}</td>
                  <td>
                    <span class="badge bg-{{ $delivery->status === 'completed' ? 'success' : ($delivery->status === 'in_progress' ? 'primary' : 'danger') }}">
                      {{ $delivery->status === 'completed' ? 'Finalizada' : ($delivery->status === 'in_progress' ? 'Em andamento' : 'Cancelada') }}
                    </span>
                  </td>
                  <td>
                    <div class="progress">
                      <div class="progress-bar {{ 
                        $delivery->status === 'completed' ? 'bg-success' : 
                        ($delivery->status === 'in_progress' ? 'bg-primary' : 'bg-danger') 
                      }}" 
                           role="progressbar" 
                           style="width: {{ $delivery->progress }}%" 
                           aria-valuenow="{{ $delivery->progress }}" 
                           aria-valuemin="0" 
                           aria-valuemax="100">
                        {{ $delivery->progress }}%
                      </div>
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
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Inicialização dos ícones Feather
  feather.replace();

  // Inicialização do mapa
  var map = L.map('map').setView([-23.5505, -46.6333], 10);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  var currentRoute = null;

  window.highlightRoute = function(stops) {
    if (currentRoute) {
      map.removeLayer(currentRoute);
    }

    if (!stops || stops.length === 0) return;

    // Filtrar apenas paradas com coordenadas válidas
    var validStops = stops.filter(stop => 
        stop.latitude && stop.longitude && 
        !isNaN(stop.latitude) && !isNaN(stop.longitude)
    );

    if (validStops.length === 0) {
        console.log('Nenhuma coordenada válida encontrada para esta rota');
        return;
    }

    var coordinates = validStops.map(stop => [
        parseFloat(stop.latitude), 
        parseFloat(stop.longitude)
    ]);
    
    currentRoute = L.polyline(coordinates, {
        color: '#1a2438',
        weight: 3,
        opacity: 0.7
    }).addTo(map);

    // Adiciona marcadores para cada parada
    validStops.forEach((stop, index) => {
        L.marker([stop.latitude, stop.longitude])
            .bindPopup(`
                <strong>Parada ${index + 1}: ${stop.name}</strong><br>
                ${stop.street}, ${stop.number}<br>
                ${stop.district}, ${stop.city} - ${stop.state}
            `)
            .addTo(map);
    });

    // Ajusta o zoom para mostrar todas as paradas
    map.fitBounds(L.latLngBounds(coordinates));
  };
});
</script>
@endpush