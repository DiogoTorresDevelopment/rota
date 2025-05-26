@extends('layout.master')

@push('plugin-styles')
  <link href="{{ asset('assets/plugins/dropzone/dropzone.min.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/datatables-net-bs5/dataTables.bootstrap5.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" />
  <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="flex flex-col">
  

  <!-- Card Principal -->
  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Dados do motorista</h2>
        <p class="mt-1 text-sm text-gray-600">Estas informações serão utilizadas dentro do sistema.</p>
      </div>

      <!-- Tabs para Mobile -->
      <div class="sm:hidden mb-6">
        <select id="tabs-mobile" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
          <option value="basic-data">Dados básicos</option>
          <option value="documents">Imagens e documentação</option>
        </select>
      </div>

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

      <form action="{{ route('drivers.store') }}" method="POST" enctype="multipart/form-data" id="driver-form">
        @csrf
        <input type="hidden" id="driver_id" name="driver_id">
        
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
                     value="{{ old('name') }}"
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
                     value="{{ old('cpf') }}"
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
                     value="{{ old('phone') }}"
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
                     value="{{ old('email') }}"
                     placeholder="exemplo@email.com">
              @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
            </div>

            <!-- Senha -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
              <input type="password" 
                     class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                     id="password" 
                     name="password" 
                     placeholder="Deixe em branco para gerar uma senha aleatória">
              <p class="mt-1 text-sm text-gray-500">Deixe em branco para gerar uma senha aleatória</p>
              @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
              @enderror
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
                         value="{{ old('cep') }}"
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
                         value="{{ old('state') }}">
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
                         value="{{ old('city') }}">
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
                         value="{{ old('district') }}">
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
                         value="{{ old('street') }}">
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
                         value="{{ old('number') }}">
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
            <!-- Upload de Arquivos -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Upload de Arquivos</h3>
              
              <!-- Área de Drop -->
              <div class="max-w-full">
                <label class="flex justify-center w-full h-32 px-4 transition bg-white border-2 border-gray-300 border-dashed rounded-md appearance-none cursor-pointer hover:border-blue-400 focus:outline-none">
                  <span class="flex flex-col items-center space-y-2 pt-5">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3 3m0 0l-3-3m3 3V8"/>
                    </svg>
                    <span class="font-medium text-gray-600">
                      Arraste os arquivos aqui ou
                      <span class="text-blue-600 underline">procure</span>
                    </span>
                    <span class="text-xs text-gray-500">PDF, PNG, JPG (MAX. 5MB)</span>
                  </span>
                  <input type="file" name="file" class="hidden" multiple accept=".pdf,.png,.jpg,.jpeg" onchange="handleFileSelect(this)">
                </label>
              </div>

              <!-- Lista de Arquivos -->
              <div id="file-list" class="mt-6 hidden">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Arquivos Selecionados</h4>
                <div class="space-y-3" id="selected-files">
                  <!-- Os arquivos serão listados aqui -->
                </div>
              </div>
            </div>

            <!-- Documentos Enviados -->
            <div id="uploaded-documents" class="hidden">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Documentos Enviados</h3>
              <div class="space-y-4" id="documents-container">
                <!-- Template para documento -->
                <template id="document-template">
                  <div class="flex items-center justify-between p-4 bg-white border rounded-lg shadow-sm">
                    <div class="flex items-center min-w-0">
                      <svg class="w-8 h-8 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      <div class="truncate">
                        <p class="text-sm font-medium text-gray-900 truncate" data-name></p>
                        <p class="text-sm text-gray-500" data-size></p>
                      </div>
                    </div>
                    <div class="flex items-center ml-4 space-x-3">
                      <button type="button" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                      </button>
                      <button type="button" class="text-red-600 hover:text-red-800" onclick="removeFile(this)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </template>
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
            Salvar
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
  <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
@endpush

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Máscara CPF
  IMask(document.getElementById('cpf'), {
    mask: '000.000.000-00'
  });

  // Máscara Telefone
  IMask(document.getElementById('phone'), {
    mask: '(00) 00000-0000'
  });

  // Máscara CEP
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

  // Função para salvar dados básicos
  async function saveBasicData(e) {
    e.preventDefault();
    
    const form = document.getElementById('driver-form');
    const formData = new FormData(form);
    
    try {
        const response = await fetch('{{ route("drivers.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Mostra mensagem de sucesso
            await Swal.fire({
                title: 'Sucesso!',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'Continuar'
            });
            
            // Redireciona para a tela de listagem
            window.location.href = '{{ route("drivers.index") }}';
        } else {
            // Trata erros de validação
            let errorMessage = data.message;
            if (data.errors) {
                errorMessage = Object.values(data.errors).flat().join('\n');
            }
            throw new Error(errorMessage);
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

  // Adiciona o evento de submit no formulário
  const form = document.getElementById('driver-form');
  form.addEventListener('submit', saveBasicData);

  // Modifica o comportamento dos botões de tab
  tabButtons.forEach(button => {
    button.addEventListener('click', async (e) => {
        const currentTab = document.querySelector('.tab-content:not(.hidden)').id.replace('tab-', '');
        const targetTab = button.dataset.tab;

        // Se estiver na primeira tab e tentando ir para a segunda
        if (currentTab === 'basic-data' && targetTab === 'documents') {
            // Verifica se já existe um driver_id
            const driverId = document.getElementById('driver_id').value;
            
            if (!driverId) {
                e.preventDefault(); // Previne a mudança imediata de tab
                const formEvent = new Event('submit');
                form.dispatchEvent(formEvent); // Dispara o evento de submit do formulário
            } else {
                // Se já existe um driver_id, apenas muda a tab
                switchTab(targetTab);
            }
        } else {
            // Para qualquer outra mudança de tab, apenas muda sem salvar
            switchTab(targetTab);
        }
    });
  });

  // Também ajusta o comportamento para mobile
  tabsMobile.addEventListener('change', (e) => {
    const targetTab = e.target.value;
    const currentTab = document.querySelector('.tab-content:not(.hidden)').id.replace('tab-', '');

    if (currentTab === 'basic-data' && targetTab === 'documents') {
        const driverId = document.getElementById('driver_id').value;
        
        if (!driverId) {
            e.preventDefault();
            const formEvent = new Event('submit');
            form.dispatchEvent(formEvent);
        } else {
            switchTab(targetTab);
        }
    } else {
        switchTab(targetTab);
    }
  });

  // Inicializa na primeira tab
  switchTab('basic-data');
});

// Função para formatar o tamanho do arquivo
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Função para lidar com a seleção de arquivos
function handleFileSelect(input) {
    const fileList = document.getElementById('file-list');
    const selectedFiles = document.getElementById('selected-files');
    const template = document.getElementById('document-template');
    
    selectedFiles.innerHTML = ''; // Limpa a lista atual
    
    Array.from(input.files).forEach(file => {
        const clone = template.content.cloneNode(true);
        
        // Preenche os dados do arquivo
        clone.querySelector('[data-name]').textContent = file.name;
        clone.querySelector('[data-size]').textContent = formatFileSize(file.size);
        
        // Adiciona o arquivo à lista
        selectedFiles.appendChild(clone);
    });
    
    // Mostra a lista de arquivos
    fileList.classList.remove('hidden');
}

// Função para remover arquivo
function removeFile(button) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Esta ação não poderá ser revertida!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            button.closest('.flex.items-center.justify-between').remove();
            
            // Se não houver mais arquivos, esconde a lista
            const selectedFiles = document.getElementById('selected-files');
            if (!selectedFiles.hasChildNodes()) {
                document.getElementById('file-list').classList.add('hidden');
            }
            
            Swal.fire(
                'Excluído!',
                'O arquivo foi removido com sucesso.',
                'success'
            );
        }
    });
}

// Função para upload de arquivos
async function uploadFiles(driverId) {
    const fileInput = document.querySelector('input[type="file"]');
    const formData = new FormData();
    
    Array.from(fileInput.files).forEach(file => {
        formData.append('files[]', file);
    });
    formData.append('driver_id', driverId);
    
    try {
        const response = await fetch('/api/drivers/documents', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
            Swal.fire({
                title: 'Sucesso!',
                text: 'Documentos enviados com sucesso!',
                icon: 'success',
                confirmButtonText: 'Ok'
            });
            
            // Atualiza a lista de documentos
            updateDocumentsList();
        } else {
            throw new Error(data.message || 'Erro ao enviar documentos');
        }
    } catch (error) {
        Swal.fire({
            title: 'Erro!',
            text: error.message,
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    }
}
</script>
@endpush 