@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<div class="flex flex-col">

  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Dados do motorista</h2>
        <p class="mt-1 text-sm text-gray-600">Atualize as informações do motorista conforme necessário.</p>
      </div>

      <!-- Tabs para Mobile -->
      <!-- <div class="sm:hidden mb-6">
        <select id="tabs-mobile" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
          <option value="basic-data">Dados básicos</option>
          <option value="documents">Imagens e documentação</option>
        </select>
      </div> -->

      <!-- Tabs para Desktop -->
      <ul class="hidden text-sm font-medium text-center text-gray-500 rounded-lg shadow-sm sm:flex mb-6">
        <li class="w-full focus-within:z-10">
          <button type="button" 
                  data-tab="basic-data"
                  class="inline-block w-full p-4 text-gray-900 bg-gray-100 border-r border-gray-200 rounded-s-lg focus:ring-4 focus:ring-blue-300 active focus:outline-none tab-button">
            Dados básicos
          </button>
        </li>
        <!-- <li class="w-full focus-within:z-10">
          <button type="button" 
                  data-tab="documents"
                  class="inline-block w-full p-4 bg-white border-r border-gray-200 hover:text-gray-700 hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 focus:outline-none rounded-e-lg tab-button">
            Imagens e documentação
          </button>
        </li> -->
      </ul>

      <form action="{{ route('drivers.update', $driver->id) }}" method="POST" enctype="multipart/form-data" id="driver-form">
        @csrf
        @method('PUT')
        <input type="hidden" id="driver_id" name="driver_id" value="{{ $driver->id }}">
        
        <!-- Tab: Dados Básicos -->
        <div id="tab-basic-data" class="tab-content">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Nome -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
              <input type="text" 
                     class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                     id="name" 
                     name="name" 
                     value="{{ old('name', $driver->name) }}"
                     placeholder="Digite o nome completo">
              @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- CPF -->
            <div>
              <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF</label>
              <input type="text" 
                     class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('cpf') border-red-500 @enderror" 
                     id="cpf" 
                     name="cpf" 
                     value="{{ old('cpf', $driver->cpf) }}"
                     placeholder="000.000.000-00">
              @error('cpf')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Telefone -->
            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
              <input type="text" 
                     class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror" 
                     id="phone" 
                     name="phone" 
                     value="{{ old('phone', $driver->phone) }}"
                     placeholder="(00) 00000-0000">
              @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Email -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
              <input type="email" 
                     class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                     id="email" 
                     name="email" 
                     value="{{ old('email', $driver->email) }}"
                     placeholder="exemplo@email.com">
              @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Senha -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
              <input type="password" 
                     class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                     id="password" 
                     name="password" 
                     placeholder="Deixe em branco para manter a senha atual">
              <p class="mt-1 text-sm text-gray-500">Deixe em branco para manter a senha atual</p>
              @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Status -->
            <div>
              <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select name="status" 
                      id="status" 
                      class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="1" {{ old('status', $driver->status) == 1 ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ old('status', $driver->status) == 0 ? 'selected' : '' }}>Inativo</option>
              </select>
            </div>

            <!-- Endereço -->
            <div class="col-span-full">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Endereço</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- CEP -->
                <div>
                  <label for="cep" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                  <input type="text" 
                         class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('cep') border-red-500 @enderror" 
                         id="cep" 
                         name="cep" 
                         value="{{ old('cep', $driver->cep) }}"
                         placeholder="00000-000">
                  @error('cep')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Estado -->
                <div>
                  <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                  <input type="text" 
                         class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('state') border-red-500 @enderror" 
                         id="state" 
                         name="state" 
                         value="{{ old('state', $driver->state) }}">
                  @error('state')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Cidade -->
                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                  <input type="text" 
                         class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('city') border-red-500 @enderror" 
                         id="city" 
                         name="city" 
                         value="{{ old('city', $driver->city) }}">
                  @error('city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Bairro -->
                <div>
                  <label for="district" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                  <input type="text" 
                         class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('district') border-red-500 @enderror" 
                         id="district" 
                         name="district" 
                         value="{{ old('district', $driver->district) }}">
                  @error('district')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Rua -->
                <div>
                  <label for="street" class="block text-sm font-medium text-gray-700 mb-1">Rua</label>
                  <input type="text" 
                         class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('street') border-red-500 @enderror" 
                         id="street" 
                         name="street" 
                         value="{{ old('street', $driver->street) }}">
                  @error('street')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
                </div>

                <!-- Número -->
                <div>
                  <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                  <input type="text" 
                         class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('number') border-red-500 @enderror" 
                         id="number" 
                         name="number" 
                         value="{{ old('number', $driver->number) }}">
                  @error('number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                  @enderror
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Documentos -->
        <div id="tab-documents" class="tab-content hidden">
          <div class="space-y-6">
            <!-- Lista de documentos existentes -->
            <div id="existing-documents" class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Documentos Existentes</h3>
                <div class="space-y-4">
                    @foreach($driver->documents as $document)
                    <div class="flex items-center justify-between p-4 bg-white border rounded-lg shadow-sm" id="doc-{{ $document->id }}">
                        <div class="flex items-center min-w-0">
                            @if(in_array(strtolower($document->type), ['pdf']))
                                <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            @else
                                <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                            <div class="truncate">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $document->original_name }}</p>
                                <p class="text-sm text-gray-500">{{ strtoupper($document->type) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Botão Visualizar -->
                            <a href="{{ asset('storage/' . $document->file_path) }}" 
                               target="_blank"
                               class="text-blue-600 hover:text-blue-900"
                               title="Visualizar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <!-- Botão Download -->
                            <a href="{{ asset('storage/' . $document->file_path) }}" 
                               download="{{ $document->original_name }}"
                               class="text-green-600 hover:text-green-900"
                               title="Baixar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                            </a>
                            <!-- Botão Excluir -->
                            <button type="button" 
                                    onclick="deleteDocument({{ $document->id }})"
                                    class="text-red-600 hover:text-red-900"
                                    title="Excluir">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Upload de novos documentos -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Adicionar Novos Documentos</h3>
              <div class="flex items-center justify-center w-full">
                <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                  <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                    </svg>
                    <p class="mb-2 text-sm text-gray-500">
                      <span class="font-semibold">Clique para fazer upload</span> ou arraste e solte
                    </p>
                    <p class="text-xs text-gray-500">PDF, PNG, JPG ou JPEG (Máx. 5MB)</p>
                  </div>
                  <input id="dropzone-file" 
                         name="files[]"
                         type="file" 
                         class="hidden" 
                         multiple 
                         accept=".pdf,.png,.jpg,.jpeg" />
                </label>
              </div>

              <!-- Preview dos arquivos selecionados -->
              <div id="file-preview" class="mt-6 hidden">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Arquivos Selecionados</h4>
                <div class="space-y-3" id="selected-files"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-end space-x-2 mt-6">
          <a href="{{ route('drivers.index') }}" 
             class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Cancelar
          </a>
          <button type="submit" 
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Atualizar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('plugin-scripts')
  <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
  <script src="https://unpkg.com/imask"></script>
@endpush

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Máscaras
    IMask(document.getElementById('cpf'), {
        mask: '000.000.000-00'
    });

    IMask(document.getElementById('phone'), {
        mask: '(00) 00000-0000'
    });

    IMask(document.getElementById('cep'), {
        mask: '00000-000'
    });

    // Busca CEP
    $('#cep').blur(function() {
        const cep = $(this).val().replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        $('#state').val(data.uf);
                        $('#city').val(data.localidade);
                        $('#district').val(data.bairro);
                        $('#street').val(data.logradouro);
                    }
                });
        }
    });

    // Controle das Tabs
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    const tabsMobile = document.getElementById('tabs-mobile');

    function switchTab(tabId) {
        tabButtons.forEach(button => {
            if (button.dataset.tab === tabId) {
                button.classList.add('text-gray-900', 'bg-gray-100');
                button.classList.remove('bg-white');
            } else {
                button.classList.remove('text-gray-900', 'bg-gray-100');
                button.classList.add('bg-white');
            }
        });

        tabContents.forEach(content => {
            if (content.id === `tab-${tabId}`) {
                content.classList.remove('hidden');
            } else {
                content.classList.add('hidden');
            }
        });
    }

    // Inicializa na primeira tab
    switchTab('basic-data');

    // Event listeners para as tabs
    tabButtons.forEach(button => {
        button.addEventListener('click', () => switchTab(button.dataset.tab));
    });

    tabsMobile.addEventListener('change', (e) => switchTab(e.target.value));

    // Função para atualizar formulário
    function updateDriver(e) {
        // Remove o preventDefault para permitir o envio normal do formulário
        // e.preventDefault();
        
        // Remove todo o código AJAX e deixa o formulário ser enviado normalmente
        return true;
    }

    // Adiciona o evento de submit no formulário
    const form = document.getElementById('driver-form');
    form.addEventListener('submit', updateDriver);

    // Funções de utilidade
    function validateFile(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB em bytes
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        
        if (file.size > maxSize) {
            return `O arquivo "${file.name}" excede o tamanho máximo de 5MB`;
        }
        
        if (!allowedTypes.includes(file.type)) {
            return `O arquivo "${file.name}" não é um tipo permitido (PDF, PNG, JPG)`;
        }
        
        return null; // arquivo válido
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function checkFileList() {
        const filePreview = document.getElementById('file-preview');
        const selectedFiles = document.getElementById('selected-files');
        if (selectedFiles.children.length === 0) {
            filePreview.classList.add('hidden');
            document.getElementById('dropzone-file').value = '';
        }
    }

    // Função para upload dos arquivos
    async function uploadFiles() {
        const input = document.getElementById('dropzone-file');
        if (!input.files.length) return;

        const formData = new FormData();
        const invalidFiles = [];
        
        // Valida cada arquivo antes de adicionar ao FormData
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const error = validateFile(file);
            
            if (error) {
                invalidFiles.push(error);
            } else {
                formData.append('files[]', file);
            }
        }
        
        // Se houver arquivos inválidos, mostra os erros
        if (invalidFiles.length > 0) {
            await Swal.fire({
                title: 'Atenção!',
                html: 'Os seguintes arquivos não puderam ser enviados:<br><br>' + 
                      invalidFiles.join('<br>'),
                icon: 'warning',
                confirmButtonText: 'Ok'
            });
            
            if (invalidFiles.length === input.files.length) return;
        }
        
        formData.append('driver_id', document.getElementById('driver_id').value);
        
        try {
            const response = await fetch('{{ route("drivers.documents.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                await Swal.fire({
                    title: 'Sucesso!',
                    text: 'Documentos válidos foram enviados com sucesso!',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
                
                input.value = '';
                document.getElementById('file-preview').classList.add('hidden');
                document.getElementById('selected-files').innerHTML = '';
                
                await loadExistingDocuments();
            } else {
                throw new Error(data.message || 'Erro ao enviar documentos');
            }
        } catch (error) {
            await Swal.fire({
                title: 'Erro!',
                text: error.message,
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        }
    }

    // Função para lidar com a seleção de arquivos
    async function handleFileSelect(input) {
        const filePreview = document.getElementById('file-preview');
        const selectedFiles = document.getElementById('selected-files');
        selectedFiles.innerHTML = '';
        
        Array.from(input.files).forEach(file => {
            const error = validateFile(file);
            const fileDiv = document.createElement('div');
            fileDiv.className = 'flex items-center justify-between p-4 bg-white border rounded-lg shadow-sm';
            
            const isPDF = file.type === 'application/pdf';
            const fileIcon = isPDF ? `
                <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            ` : `
                <svg class="w-8 h-8 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            `;

            fileDiv.innerHTML = `
                <div class="flex items-center min-w-0">
                    ${fileIcon}
                    <div class="truncate">
                        <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                        <p class="text-sm ${error ? 'text-red-500' : 'text-gray-500'}">${error || formatFileSize(file.size)}</p>
                    </div>
                </div>
                <button type="button" class="text-red-600 hover:text-red-900" onclick="this.closest('div').remove(); checkFileList();">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            `;
            selectedFiles.appendChild(fileDiv);
        });
        
        filePreview.classList.toggle('hidden', input.files.length === 0);

        if (input.files.length > 0) {
            await uploadFiles();
        }
    }

    // Configuração dos event listeners
    const dropzoneInput = document.getElementById('dropzone-file');
    const dropzoneLabel = document.querySelector('label[for="dropzone-file"]');

    dropzoneInput.addEventListener('change', function() {
        handleFileSelect(this);
    });

    dropzoneLabel.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzoneLabel.classList.add('bg-gray-100');
    });

    dropzoneLabel.addEventListener('dragleave', () => {
        dropzoneLabel.classList.remove('bg-gray-100');
    });

    dropzoneLabel.addEventListener('drop', async (e) => {
        e.preventDefault();
        dropzoneLabel.classList.remove('bg-gray-100');
        
        const input = document.getElementById('dropzone-file');
        input.files = e.dataTransfer.files;
        await handleFileSelect(input);
    });
});
</script>
@endpush 