<!-- Informações da Rota -->
<div class="mb-8">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $route->name ?? '') }}"
                   class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                   placeholder="Digite o nome da rota">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
            <input type="date" 
                   id="start_date" 
                   name="start_date" 
                   value="{{ old('start_date', isset($route->start_date) ? $route->start_date->format('Y-m-d') : '') }}"
                   class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            @error('start_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<!-- Informações do Motorista -->
<div class="mb-8">
    <h3 class="text-lg font-medium text-gray-900 mb-6">Informações do motorista</h3>
    <div class="grid grid-cols-4 gap-6">
        <div>
            <label for="driver_id" class="block text-sm font-medium text-gray-700 mb-2">Motorista</label>
            <select id="driver_id" 
                    name="driver_id" 
                    class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Selecione um motorista</option>
                @foreach($drivers as $driver)
                    <option value="{{ $driver->id }}" 
                            data-cpf="{{ $driver->cpf }}"
                            data-phone="{{ $driver->phone }}"
                            data-email="{{ $driver->email }}"
                            {{ old('driver_id', $route->driver_id ?? '') == $driver->id ? 'selected' : '' }}>
                        {{ $driver->name }}
                    </option>
                @endforeach
            </select>
            @error('driver_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="driver_cpf" class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
            <input type="text" id="driver_cpf" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
        <div>
            <label for="driver_phone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
            <input type="text" id="driver_phone" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
        <div>
            <label for="driver_email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
            <input type="email" id="driver_email" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
    </div>
</div>

<!-- Informações do Caminhão -->
<div class="mb-8">
    <h3 class="text-lg font-medium text-gray-900 mb-6">Informações do caminhão</h3>
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div>
            <label for="truck_id" class="block text-sm font-medium text-gray-700 mb-2">Caminhão</label>
            <select id="truck_id" 
                    name="truck_id" 
                    class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="">Selecione um caminhão</option>
                @foreach($trucks as $truck)
                    <option value="{{ $truck->id }}"
                            data-cor="{{ $truck->cor }}"
                            data-combustivel="{{ $truck->tipo_combustivel }}"
                            data-modelo="{{ $truck->modelo }}"
                            data-marca="{{ $truck->marca }}"
                            data-chassi="{{ $truck->chassi }}"
                            data-placa="{{ $truck->placa }}"
                            data-quilometragem="{{ $truck->quilometragem }}"
                            data-ultima_revisao="{{ $truck->ultima_revisao ? \Carbon\Carbon::parse($truck->ultima_revisao)->format('Y-m-d') : '' }}"
                            {{ old('truck_id', $route->truck_id ?? '') == $truck->id ? 'selected' : '' }}>
                        {{ $truck->marca }} - {{ $truck->modelo }} ({{ $truck->placa }})
                    </option>
                @endforeach
            </select>
            @error('truck_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="truck_marca" class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
            <input type="text" id="truck_marca" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
        <div>
            <label for="truck_modelo" class="block text-sm font-medium text-gray-700 mb-2">Modelo</label>
            <input type="text" id="truck_modelo" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
        <div>
            <label for="truck_cor" class="block text-sm font-medium text-gray-700 mb-2">Cor</label>
            <input type="text" id="truck_cor" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
    </div>
    <div class="grid grid-cols-4 gap-6 mb-6">
        <div>
            <label for="truck_combustivel" class="block text-sm font-medium text-gray-700 mb-2">Combustível</label>
            <input type="text" id="truck_combustivel" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
        <div>
            <label for="truck_chassi" class="block text-sm font-medium text-gray-700 mb-2">Chassi</label>
            <input type="text" id="truck_chassi" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
        <div>
            <label for="truck_placa" class="block text-sm font-medium text-gray-700 mb-2">Placa</label>
            <input type="text" id="truck_placa" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
        <div>
            <label for="current_mileage" class="block text-sm font-medium text-gray-700 mb-2">Quilometragem Atual</label>
            <input type="number" 
                   step="0.01" 
                   id="current_mileage" 
                   name="current_mileage" 
                   value="{{ old('current_mileage', $route->current_mileage ?? '') }}"
                   class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            @error('current_mileage')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="grid grid-cols-4 gap-6">
        <div>
            <label for="truck_quilometragem" class="block text-sm font-medium text-gray-700 mb-2">Quilometragem do Caminhão</label>
            <input type="text" id="truck_quilometragem" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
        <div>
            <label for="truck_ultima_revisao" class="block text-sm font-medium text-gray-700 mb-2">Última Revisão</label>
            <input type="date" id="truck_ultima_revisao" class="w-full h-12 px-4 rounded-lg bg-gray-50 border-gray-300" readonly>
        </div>
    </div>
</div>

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Atualiza informações do motorista
    const driverSelect = document.getElementById('driver_id');
    const updateDriverInfo = () => {
        const selectedOption = driverSelect.options[driverSelect.selectedIndex];
        document.getElementById('driver_cpf').value = selectedOption.dataset.cpf || '';
        document.getElementById('driver_phone').value = selectedOption.dataset.phone || '';
        document.getElementById('driver_email').value = selectedOption.dataset.email || '';
    };
    driverSelect.addEventListener('change', updateDriverInfo);
    updateDriverInfo(); // Executa na carga inicial

    // Atualiza informações do caminhão
    const truckSelect = document.getElementById('truck_id');
    const updateTruckInfo = () => {
        const selectedOption = truckSelect.options[truckSelect.selectedIndex];
        document.getElementById('truck_marca').value = selectedOption.dataset.marca || '';
        document.getElementById('truck_modelo').value = selectedOption.dataset.modelo || '';
        document.getElementById('truck_cor').value = selectedOption.dataset.cor || '';
        document.getElementById('truck_combustivel').value = selectedOption.dataset.combustivel || '';
        document.getElementById('truck_chassi').value = selectedOption.dataset.chassi || '';
        document.getElementById('truck_placa').value = selectedOption.dataset.placa || '';
        document.getElementById('truck_quilometragem').value = selectedOption.dataset.quilometragem || '';
        document.getElementById('truck_ultima_revisao').value = selectedOption.dataset.ultima_revisao || '';
        
        // Preenche a quilometragem atual com a quilometragem do caminhão se estiver vazia
        const currentMileageInput = document.getElementById('current_mileage');
        if (!currentMileageInput.value) {
            currentMileageInput.value = selectedOption.dataset.quilometragem || '';
        }
    };
    truckSelect.addEventListener('change', updateTruckInfo);
    updateTruckInfo(); // Executa na carga inicial

    // Adiciona handler para o formulário
    const form = document.querySelector('form');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(form);
            formData.append('step', '1'); // Indica que estamos na etapa 1

            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                // Se salvou com sucesso, redireciona para a próxima etapa
                window.location.href = `${window.location.pathname}?step=2&route_id=${data.route_id}`;
            } else {
                throw new Error(data.message || 'Erro ao salvar os dados');
            }
        } catch (error) {
            alert('Erro ao salvar: ' + error.message);
        }
    });
});
</script>
@endpush 