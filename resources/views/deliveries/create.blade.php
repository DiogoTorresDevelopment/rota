@extends('layouts.app')

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
                        <div class="form-group">
                            <label for="route_id">Rota</label>
                            <select class="form-control" id="route_id" name="route_id" required>
                                <option value="">Selecione uma rota</option>
                                @foreach($availableRoutes as $route)
                                    <option value="{{ $route->id }}">{{ $route->name }}</option>
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