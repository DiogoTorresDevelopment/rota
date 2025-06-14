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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title">Histórico da Entrega #{{ $delivery->id }}</h6>
                        <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-sm btn-secondary">Voltar</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Parada</th>
                                    <th>Motorista</th>
                                    <th>Caminhão</th>
                                    <th>Carrocerias</th>
                                </tr>
                            </thead>
                            <tbody id="history-body"></tbody>
                        </table>
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

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch("{{ route('deliveries.history', $delivery) }}")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const body = document.getElementById('history-body');
                data.data.forEach(entry => {
                    const tr = document.createElement('tr');
                    const stopName = entry.delivery_stop ? entry.delivery_stop.delivery_route_stop.name : '-';
                    const carros = (entry.carroceria_ids || []).join(', ');
                    tr.innerHTML = `<td>${new Date(entry.created_at).toLocaleString()}</td>` +
                                   `<td>${stopName}</td>` +
                                   `<td>${entry.driver ? entry.driver.name : entry.driver_id}</td>` +
                                   `<td>${entry.truck ? entry.truck.placa : entry.truck_id}</td>` +
                                   `<td>${carros}</td>`;
                    body.appendChild(tr);
                });
            } else {
                Swal.fire('Erro', 'Não foi possível carregar o histórico', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Erro', 'Não foi possível carregar o histórico', 'error');
        });
});
</script>
@endpush
