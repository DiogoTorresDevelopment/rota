<div class="space-y-8">
    {{-- Formulário Principal --}}
    <form id="route-form" method="POST" action="{{ route('routes.store') }}">
        @csrf
        {{-- Seção de Informações do Destino --}}
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Informações do Destino</h3>
                    <p class="text-sm text-gray-600">Adicione aqui os destinos necessários.</p>
                </div>
                <button type="button" 
                        onclick="showDestinoForm()"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    + Adicionar
                </button>
            </div>

            {{-- Lista de Destinos Cadastrados --}}
            <div class="bg-white rounded-lg shadow-sm divide-y divide-gray-200" id="destinos-list">
                {{-- Os destinos serão adicionados aqui dinamicamente --}}
            </div>

            {{-- Input hidden para armazenar os destinos --}}
            <input type="hidden" name="destinations" id="destinations-data" value="">
        </div>

        {{-- Botão de Salvar --}}
        <div class="flex justify-end">
            <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Salvar Destinos
            </button>
        </div>
    </form>

    {{-- Modal de Cadastro de Destino --}}
    <div id="destino-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" style="z-index: 1000;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Cadastro de Destino</h3>
                        <button type="button" onclick="hideDestinoForm()" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Fechar</span>
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form id="destino-form" class="space-y-6" onsubmit="return addDestino(event)">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="destino_name" class="block text-sm font-medium text-gray-700">Nome do Destino</label>
                                <input type="text" id="destino_name" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="destino_cep" class="block text-sm font-medium text-gray-700">CEP</label>
                                <input type="text" id="destino_cep" name="cep" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="destino_estado" class="block text-sm font-medium text-gray-700">Estado</label>
                                <input type="text" id="destino_estado" name="state" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="destino_cidade" class="block text-sm font-medium text-gray-700">Cidade</label>
                                <input type="text" id="destino_cidade" name="city" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="destino_endereco" class="block text-sm font-medium text-gray-700">Endereço</label>
                                <input type="text" id="destino_endereco" name="street" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="destino_numero" class="block text-sm font-medium text-gray-700">Número</label>
                                <input type="text" id="destino_numero" name="number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="hideDestinoForm()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Adicionar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const destinoForm = document.getElementById('destino-form');
    const routeForm = document.getElementById('route-form');
    const modal = document.getElementById('destino-modal');
    let destinations = [];
    let tempId = 1;

    // Carrega os destinos existentes se estiver editando
    @if(isset($route) && $route->stops->count() > 0)
        @foreach($route->stops as $stop)
            destinations.push({
                tempId: tempId++,
                name: "{{ $stop->name }}",
                cep: "{{ $stop->cep }}",
                state: "{{ $stop->state }}",
                city: "{{ $stop->city }}",
                street: "{{ $stop->street }}",
                number: "{{ $stop->number }}"
            });

            // Adiciona o destino na lista visual
            document.getElementById('destinos-list').insertAdjacentHTML('beforeend', `
                <div class="p-4 flex items-center justify-between" id="destino-item-${tempId - 1}">
                    <div class="flex items-center space-x-3">
                        <span class="flex-shrink-0 h-6 w-6 flex items-center justify-center rounded-full bg-gray-200 text-sm font-medium text-gray-600">
                            ${destinations.length}
                        </span>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">${"{{ $stop->name }}"}</h4>
                            <p class="text-sm text-gray-500">${"{{ $stop->city }}"} - ${"{{ $stop->state }}"}</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeDestino(${tempId - 1})" class="text-red-600 hover:text-red-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            `);
        @endforeach
        
        // Atualiza o input hidden com os destinos carregados
        updateDestinationsData();
    @endif

    window.showDestinoForm = function() {
        modal.classList.remove('hidden');
    }

    window.hideDestinoForm = function() {
        modal.classList.add('hidden');
        destinoForm.reset();
    }

    window.removeDestino = function(tempId) {
        if (confirm('Tem certeza que deseja remover este destino?')) {
            document.getElementById(`destino-item-${tempId}`).remove();
            destinations = destinations.filter(d => d.tempId !== tempId);
            updateDestinationsData();
        }
    }

    window.addDestino = function(e) {
        e.preventDefault();
        
        const formData = new FormData(destinoForm);
        const destino = {
            tempId: tempId++,
            name: formData.get('name'),
            cep: formData.get('cep'),
            state: formData.get('state'),
            city: formData.get('city'),
            street: formData.get('street'),
            number: formData.get('number')
        };

        destinations.push(destino);
        
        const html = `
            <div class="p-4 flex items-center justify-between" id="destino-item-${destino.tempId}">
                <div class="flex items-center space-x-3">
                    <span class="flex-shrink-0 h-6 w-6 flex items-center justify-center rounded-full bg-gray-200 text-sm font-medium text-gray-600">
                        ${destinations.length}
                    </span>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900">${destino.name}</h4>
                        <p class="text-sm text-gray-500">${destino.city} - ${destino.state}</p>
                    </div>
                </div>
                <button type="button" onclick="removeDestino(${destino.tempId})" class="text-red-600 hover:text-red-800">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        `;
        
        document.getElementById('destinos-list').insertAdjacentHTML('beforeend', html);
        updateDestinationsData();
        
        // Limpa o formulário sem fechar o modal
        destinoForm.reset();
        
        // Opcional: Foca no primeiro campo para facilitar nova entrada
        document.getElementById('destino_name').focus();
        
        return false;
    }

    function updateDestinationsData() {
        const input = document.getElementById('destinations-data');
        input.value = JSON.stringify(destinations);
        console.log('Destinations data:', destinations); // Para debug
        console.log('Input value:', input.value); // Para debug
    }

    // Busca CEP
    document.getElementById('destino_cep').addEventListener('blur', async function() {
        const cep = this.value.replace(/\D/g, '');
        if (cep.length === 8) {
            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();
                if (!data.erro) {
                    document.getElementById('destino_estado').value = data.uf;
                    document.getElementById('destino_cidade').value = data.localidade;
                    document.getElementById('destino_endereco').value = data.logradouro;
                    document.getElementById('destino_numero').focus();
                }
            } catch (error) {
                console.error('Erro ao buscar CEP:', error);
            }
        }
    });

    // Máscara para CEP
    document.getElementById('destino_cep').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 8) value = value.slice(0, 8);
        if (value.length > 5) {
            value = value.slice(0, 5) + '-' + value.slice(5);
        }
        e.target.value = value;
    });

    // Handler para o formulário principal
    routeForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (destinations.length === 0) {
            alert('Adicione pelo menos um destino antes de salvar.');
            return;
        }

            const formData = new FormData(routeForm);
            formData.append('step', '3');
            
            // Pega o route_id da URL
            const urlParams = new URLSearchParams(window.location.search);
            const routeId = urlParams.get('route_id');
            if (routeId) {
                formData.append('route_id', routeId);
            }

            // Log dos dados sendo enviados
            console.log('Sending data:', {
                destinations: destinations,
                route_id: routeId,
                step: 3
            });

            const response = await fetch(routeForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            // Log da resposta bruta
            const responseText = await response.text();
            console.log('Raw response:', responseText);

            // Tenta fazer o parse do JSON apenas se houver conteúdo
            let data;
            if (responseText) {
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    // Se a resposta foi ok mas não é JSON válido, consideramos sucesso
                    if (response.ok) {
                        alert('Destinos salvos com sucesso!');
                        // Opcional: redirecionar para a próxima etapa ou listagem
                        window.location.href = `${window.location.pathname}?step=4&route_id=${routeId}`;
                        return;
                    }
                }
            }

            // Se temos dados JSON válidos
            if (data && data.success) {
                alert('Destinos salvos com sucesso!');
                // Opcional: redirecionar para a próxima etapa ou listagem
                window.location.href = `${window.location.pathname}?step=4&route_id=${data.route_id}`;
            } else if (data && !data.success) {
                throw new Error(data.message || 'Erro ao salvar os dados');
            } else {
                // Se chegamos aqui, a resposta não foi nem sucesso nem erro conhecido
                if (response.ok) {
                    alert('Destinos salvos com sucesso!');
                    window.location.href = `${window.location.pathname}?step=4&route_id=${routeId}`;
                } else {
                    throw new Error('Resposta inválida do servidor');
                }
            }
    });
});
</script>
@endpush 