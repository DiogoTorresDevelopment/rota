<div class="space-y-8">
    {{-- Origem --}}
    <div class="mb-8">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Origem</h3>
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="origem" class="block text-sm font-medium text-gray-700 mb-2">Origem</label>
                <input type="text" 
                       id="origem" 
                       name="origin[name]" 
                       value="{{ old('origin.name', $route->origin()?->name ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Digite o nome da origem">
                @error('origin.name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="horario_saida" class="block text-sm font-medium text-gray-700 mb-2">Horário Saída Padrão</label>
                <input type="time" 
                       id="horario_saida" 
                       name="origin[schedule]" 
                       value="{{ old('origin.schedule', $route->origin()?->schedule?->format('H:i') ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('origin.schedule')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-3 gap-6">
            <div>
                <div class="relative">
                    <label for="cep_origem" class="block text-sm font-medium text-gray-700 mb-2">CEP</label>
                    <input type="text" 
                           id="cep_origem" 
                           name="origin[cep]" 
                           value="{{ old('origin.cep', $route->origin()?->cep ?? '') }}"
                           class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="00000-000"
                           required>
                    @error('origin.cep')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div>
                <label for="estado_origem" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <input type="text" 
                       id="estado_origem" 
                       name="origin[state]" 
                       value="{{ old('origin.state', $route->origin()?->state ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('origin.state')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="cidade_origem" class="block text-sm font-medium text-gray-700 mb-2">Cidade</label>
                <input type="text" 
                       id="cidade_origem" 
                       name="origin[city]" 
                       value="{{ old('origin.city', $route->origin()?->city ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('origin.city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-2 gap-6 mt-6">
            <div>
                <label for="endereco_origem" class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
                <input type="text" 
                       id="endereco_origem" 
                       name="origin[street]" 
                       value="{{ old('origin.street', $route->origin()?->street ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('origin.street')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="numero_origem" class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                <input type="text" 
                       id="numero_origem" 
                       name="origin[number]" 
                       value="{{ old('origin.number', $route->origin()?->number ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('origin.number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Destino Final --}}
    <div class="mb-8 mt-3">
        <h3 class="text-lg font-medium text-gray-900 mb-6">Destino Final</h3>
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="destino" class="block text-sm font-medium text-gray-700 mb-2">Destino</label>
                <input type="text" 
                       id="destino" 
                       name="destination[name]" 
                       value="{{ old('destination.name', $route->destination()?->name ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Digite o nome do destino">
                @error('destination.name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="cep_destino" class="block text-sm font-medium text-gray-700 mb-2">CEP</label>
                <input type="text" 
                       id="cep_destino" 
                       name="destination[cep]" 
                       value="{{ old('destination.cep', $route->destination()?->cep ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                       onblur="buscarCep('destino')"
                       placeholder="00000-000">
                @error('destination.cep')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label for="estado_destino" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <input type="text" 
                       id="estado_destino" 
                       name="destination[state]" 
                       value="{{ old('destination.state', $route->destination()?->state ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('destination.state')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="cidade_destino" class="block text-sm font-medium text-gray-700 mb-2">Cidade</label>
                <input type="text" 
                       id="cidade_destino" 
                       name="destination[city]" 
                       value="{{ old('destination.city', $route->destination()?->city ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('destination.city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label for="endereco_destino" class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
                <input type="text" 
                       id="endereco_destino" 
                       name="destination[street]" 
                       value="{{ old('destination.street', $route->destination()?->street ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('destination.street')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="numero_destino" class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                <input type="text" 
                       id="numero_destino" 
                       name="destination[number]" 
                       value="{{ old('destination.number', $route->destination()?->number ?? '') }}"
                       class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('destination.number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para aplicar máscara de CEP
    function maskCEP(input) {
        return input.replace(/\D/g, '')
                   .replace(/(\d{5})(\d)/, '$1-$2')
                   .replace(/(-\d{3})\d+?$/, '$1');
    }

    // Função para buscar CEP
    async function buscarCep(tipo) {
        const cepInput = document.getElementById(`cep_${tipo}`);
        const cep = cepInput.value.replace(/\D/g, '');
        
        if (cep.length !== 8) return;

        try {
            // Mostrar loading ou desabilitar campos
            const campos = ['estado', 'cidade', 'endereco'].map(field => document.getElementById(`${field}_${tipo}`));
            campos.forEach(campo => {
                campo.value = 'Carregando...';
                campo.disabled = true;
            });

            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            const data = await response.json();

            if (!data.erro) {
                document.getElementById(`estado_${tipo}`).value = data.uf;
                document.getElementById(`cidade_${tipo}`).value = data.localidade;
                document.getElementById(`endereco_${tipo}`).value = data.logradouro;
                
                // Habilitar campos novamente
                campos.forEach(campo => campo.disabled = false);
                
                // Focar no campo número após preenchimento
                document.getElementById(`numero_${tipo}`).focus();
            } else {
                // Limpar e habilitar campos em caso de erro
                campos.forEach(campo => {
                    campo.value = '';
                    campo.disabled = false;
                });
                alert('CEP não encontrado');
            }
        } catch (error) {
            console.error('Erro ao buscar CEP:', error);
            alert('Erro ao buscar CEP. Tente novamente.');
        }
    }

    // Aplicar máscaras e listeners nos campos
    const masks = {
        cep: value => value.replace(/\D/g, '').replace(/(\d{5})(\d{3})/, '$1-$2'),
        phone: value => value.replace(/\D/g, '').replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3'),
        number: value => value.replace(/\D/g, '')
    };

    // Aplicar máscaras nos campos
    document.querySelectorAll('input').forEach(input => {
        const field = input.id;

        if (field.includes('cep')) {
            input.addEventListener('input', function(e) {
                e.target.value = maskCEP(e.target.value);
            });

            input.addEventListener('blur', function(e) {
                const tipo = field.includes('origem') ? 'origem' : 'destino';
                buscarCep(tipo);
            });
        }

        if (field.includes('numero')) {
            input.addEventListener('input', function(e) {
                e.target.value = masks.number(e.target.value);
            });
        }
    });

    // Validações adicionais
    const validateFields = {
        cep: value => value.replace(/\D/g, '').length === 8,
        required: value => value.trim().length > 0
    };

    // Adicionar validações nos campos obrigatórios
    document.querySelectorAll('input[required]').forEach(input => {
        input.addEventListener('blur', function(e) {
            const field = e.target;
            const value = field.value;
            const isValid = field.id.includes('cep') 
                ? validateFields.cep(value) 
                : validateFields.required(value);

            if (!isValid) {
                field.classList.add('border-red-500');
                // Você pode adicionar uma mensagem de erro aqui se desejar
            } else {
                field.classList.remove('border-red-500');
            }
        });
    });

    // Função para limpar campos de endereço
    function limparCamposEndereco(tipo) {
        ['estado', 'cidade', 'endereco', 'numero'].forEach(field => {
            const input = document.getElementById(`${field}_${tipo}`);
            if (input) {
                input.value = '';
                input.disabled = false;
            }
        });
    }

    // Adicionar botões de limpar nos campos de CEP
    document.querySelectorAll('input[id$="_cep"]').forEach(input => {
        const wrapper = input.parentElement;
        const clearButton = document.createElement('button');
        clearButton.type = 'button';
        clearButton.className = 'absolute right-10 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600';
        clearButton.innerHTML = '×';
        clearButton.onclick = function() {
            input.value = '';
            const tipo = input.id.includes('origem') ? 'origem' : 'destino';
            limparCamposEndereco(tipo);
            input.focus();
        };
        
        // Fazer o wrapper relative se ainda não for
        if (window.getComputedStyle(wrapper).position === 'static') {
            wrapper.style.position = 'relative';
        }
        
        wrapper.appendChild(clearButton);
    });

    // Adiciona handler para o formulário
    const form = document.querySelector('form');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(form);
            formData.append('step', '2'); // Indica que estamos na etapa 2
            
            // Pega o route_id da URL
            const urlParams = new URLSearchParams(window.location.search);
            const routeId = urlParams.get('route_id');
            if (routeId) {
                formData.append('route_id', routeId);
            }

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Erro ao salvar os dados');
            }

            const data = await response.json();

            if (data.success) {
                // Se salvou com sucesso, redireciona para a próxima etapa
                window.location.href = `${window.location.pathname}?step=3&route_id=${data.route_id}`;
            } else {
                throw new Error(data.message || 'Erro ao salvar os dados');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao salvar: ' + error.message);
        }
    });
});
</script>
@endpush 