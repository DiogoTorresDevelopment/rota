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
                                                        <div class="relative group shadow rounded-lg overflow-hidden bg-white">
                                                            <img src="{{ $photo->url }}" class="w-full h-32 object-cover" alt="{{ $photo->original_name }}">
                                                            <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity z-10" onclick="removeFile('{{ $photo->id }}')">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>
                                                            <div class="p-2">
                                                                <p class="card-text text-xs font-semibold truncate">{{ $photo->original_name }}</p>
                                                                @if($photo->description)
                                                                    <p class="card-text text-xs text-gray-500 truncate">{{ $photo->description }}</p>
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
                                            <div class="flex items-center justify-center w-full">
                                                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                        </svg>
                                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Clique para fazer upload</span> ou arraste e solte</p>
                                                        <p class="text-xs text-gray-500">PNG, JPG ou GIF (MÁX. 2MB)</p>
                                                    </div>
                                                    <input id="dropzone-file" type="file" class="hidden" multiple accept="image/*" />
                                                </label>
                                            </div>
                                            <div id="preview-container" class="grid grid-cols-4 gap-4 mt-4"></div>
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
    const dropzoneFile = document.getElementById('dropzone-file');
    const previewContainer = document.getElementById('preview-container');
    const form = document.getElementById('edit-stop-form');
    let uploadedFiles = [];
    let uploadedFileIds = [];

    // Handle file selection
    dropzoneFile.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Handle drag and drop
    const dropzone = document.querySelector('label[for="dropzone-file"]');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, unhighlight, false);
    });

    function highlight(e) {
        dropzone.classList.add('border-blue-500', 'bg-blue-50');
    }

    function unhighlight(e) {
        dropzone.classList.remove('border-blue-500', 'bg-blue-50');
    }

    dropzone.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    async function handleFiles(files) {
        for (const file of files) {
            if (file.size > 2 * 1024 * 1024) { // 2MB limit
                Swal.fire({
                    title: 'Erro!',
                    text: 'O arquivo excede o limite de 2MB',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                continue;
            }

            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    title: 'Erro!',
                    text: 'Apenas imagens são permitidas',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                continue;
            }

            try {
                const formData = new FormData();
                formData.append('photo', file);
                formData.append('delivery_id', document.querySelector('input[name="delivery_id"]').value);
                formData.append('stop_id', document.querySelector('input[name="stop_id"]').value);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch('/deliveries/upload-photo', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Erro ao fazer upload do arquivo');
                }

                const data = await response.json();
                if (data.success) {
                    uploadedFileIds.push(data.photo_id);
                    previewFile(file, data.photo_id);
                } else {
                    throw new Error(data.message || 'Erro ao fazer upload do arquivo');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Erro!',
                    text: error.message || 'Erro ao fazer upload do arquivo',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
    }

    function previewFile(file, photoId) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = function() {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <div class="relative group">
                    <img src="${reader.result}" class="w-full h-32 object-cover rounded-lg" />
                    <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity" onclick="removeFile('${photoId}')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            previewContainer.appendChild(div);
        }
    }

    window.removeFile = async function(photoId) {
        try {
            const response = await fetch(`/deliveries/delete-photo/${photoId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Erro ao remover arquivo');
            }

            const data = await response.json();
            if (data.success) {
                // Remove o card da foto existente
                const photoCard = document.querySelector(`button[onclick="removeFile('${photoId}')"]`);
                if (photoCard) {
                    const card = photoCard.closest('.col-md-3');
                    if (card) card.remove();
                }
                // Remove também do array de novos uploads, se existir
                uploadedFileIds = uploadedFileIds.filter(id => id !== photoId);
                // Remove do preview de novos uploads
                const previews = previewContainer.children;
                for (let i = 0; i < previews.length; i++) {
                    if (previews[i].querySelector('button').onclick.toString().includes(photoId)) {
                        previews[i].remove();
                        break;
                    }
                }
            } else {
                throw new Error(data.message || 'Erro ao remover arquivo');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'Erro!',
                text: error.message || 'Erro ao remover arquivo',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        formData.append('uploaded_photos', JSON.stringify(uploadedFileIds));
        
        // Send data via AJAX
        fetch(`/deliveries/${formData.get('delivery_id')}/stops/${formData.get('stop_id')}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Erro ao salvar as alterações');
                });
            }
            return response.json();
        })
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
                throw new Error(data.message || 'Erro ao salvar as alterações');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Erro!',
                text: error.message || 'Ocorreu um erro ao salvar as alterações.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });
});
</script>

<style>
/* Remove old dropzone styles as we're using Tailwind now */
</style>
@endpush 