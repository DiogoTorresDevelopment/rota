@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="card-title">Editar Parada</h6>
                        <a href="{{ route('deliveries.edit', $delivery) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Voltar para Entrega
                        </a>
                    </div>

                    <form id="edit-stop-form" class="forms-sample">
                        @csrf
                        <input type="hidden" name="delivery_id" value="{{ $delivery->id }}">
                        <input type="hidden" name="stop_id" value="{{ $stop->id }}">

                        <div class="row">
                            <!-- Detalhes da Parada -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Detalhes da Parada</h6>
                                        
                                        <div class="form-group">
                                            <label>Nome</label>
                                            <div class="form-control bg-light">
                                                {{ $stop->deliveryRouteStop->name }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Endereço</label>
                                            <div class="form-control bg-light">
                                                {{ $stop->deliveryRouteStop->street }}, {{ $stop->deliveryRouteStop->number }}
                                                {{ $stop->deliveryRouteStop->complement ? '- ' . $stop->deliveryRouteStop->complement : '' }}
                                                <br>
                                                {{ $stop->deliveryRouteStop->neighborhood }} - {{ $stop->deliveryRouteStop->city }}/{{ $stop->deliveryRouteStop->state }}
                                                <br>
                                                CEP: {{ $stop->deliveryRouteStop->cep }}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Ordem</label>
                                            <div class="form-control bg-light">
                                                {{ $stop->order }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status e Observações -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Status e Observações</h6>

                                        <div class="form-group">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="pending" {{ $stop->status === 'pending' ? 'selected' : '' }}>Pendente</option>
                                                <option value="completed" {{ $stop->status === 'completed' ? 'selected' : '' }}>Concluída</option>
                                                <option value="cancelled" {{ $stop->status === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Data de Conclusão</label>
                                            <input type="datetime-local" name="completed_at" class="form-control" 
                                                value="{{ $stop->completed_at ? $stop->completed_at->format('Y-m-d\TH:i') : '' }}">
                                        </div>

                                        <div class="form-group">
                                            <label>Observações</label>
                                            <textarea name="notes" class="form-control" rows="4">{{ $stop->notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fotos -->
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Fotos de Comprovante</h6>

                                        <!-- Fotos Existentes -->
                                        @if($stop->photos->count() > 0)
                                            <div class="row mb-4">
                                                @foreach($stop->photos as $photo)
                                                    <div class="col-md-3 mb-3">
                                                        <div class="card">
                                                            <img src="{{ $photo->url }}" class="card-img-top" alt="{{ $photo->original_name }}">
                                                            <div class="card-body">
                                                                <p class="card-text small">{{ $photo->original_name }}</p>
                                                                @if($photo->description)
                                                                    <p class="card-text small text-muted">{{ $photo->description }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Upload de Novas Fotos -->
                                        <div class="form-group">
                                            <label>Adicionar Novas Fotos</label>
                                            <div class="dropzone" id="photoUpload">
                                                <div class="dz-message">
                                                    Arraste fotos aqui ou clique para selecionar
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Salvar Alterações
                            </button>
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
  <script src="{{ asset('assets/plugins/dropzone/dropzone.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuração do Dropzone
    Dropzone.autoDiscover = false;
    
    const photoUpload = new Dropzone("#photoUpload", {
        url: "{{ route('deliveries.update-stop', ['delivery' => $delivery->id, 'stop' => $stop->id]) }}",
        paramName: "photos",
        maxFilesize: 2, // MB
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    // Manipula o envio do formulário
    document.getElementById('edit-stop-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Adiciona as fotos do Dropzone
        const dropzoneFiles = photoUpload.getAcceptedFiles();
        dropzoneFiles.forEach((file, index) => {
            formData.append(`photos[${index}]`, file);
        });
        
        // Envia os dados via AJAX
        fetch(`/deliveries/${formData.get('delivery_id')}/stops/${formData.get('stop_id')}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
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
                        window.location.href = `/deliveries/${formData.get('delivery_id')}/edit`;
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
.dropzone {
    border: 2px dashed #0087F7;
    border-radius: 5px;
    background: white;
    min-height: 150px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.dropzone .dz-message {
    font-weight: 400;
    color: #6c757d;
    margin: 0;
}

.dropzone .dz-preview .dz-image {
    border-radius: 5px;
}

.dropzone .dz-preview .dz-details {
    color: #6c757d;
}
</style>
@endpush 