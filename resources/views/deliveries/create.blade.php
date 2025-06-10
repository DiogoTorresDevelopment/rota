@extends('layout.master')

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Nova Entrega</h4>
                </div>
                <div class="card-body">
                    <form id="createDeliveryForm">
                        <div class="form-group mb-4">
                            <label for="route_id">Rota</label>
                            <select class="form-control" id="route_id" name="route_id" required>
                                <option value="">Selecione uma rota</option>
                                @foreach($availableRoutes as $route)
                                    <option value="{{ $route->id }}">{{ $route->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="driver_id">Motorista</label>
                            <select class="form-control" id="driver_id" name="driver_id" required>
                                <option value="">Selecione</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="truck_id">Caminhão</label>
                            <select class="form-control" id="truck_id" name="truck_id" required>
                                <option value="">Selecione</option>
                                @foreach($trucks as $truck)
                                    <option value="{{ $truck->id }}">{{ $truck->marca }} - {{ $truck->modelo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-4">
                            <label for="carroceria_ids">Carrocerias</label>
                            <select multiple class="form-control" id="carroceria_ids" name="carroceria_ids[]" required>
                                @foreach($carrocerias as $carroceria)
                                    <option value="{{ $carroceria->id }}">{{ $carroceria->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Iniciar Entrega</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#createDeliveryForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("deliveries.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    window.location.href = '{{ route("deliveries.index") }}';
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Erro ao criar entrega');
            }
        });
    });
});
</script>
@endpush
@endsection 