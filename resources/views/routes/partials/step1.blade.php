<!-- Informações da Rota -->
<div class="mb-8">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
            <input type="text" id="name" name="name" value="{{ old('name', $route->name ?? '') }}" class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Digite o nome da rota">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
            <input type="date" id="start_date" name="start_date" value="{{ old('start_date', isset($route->start_date) ? $route->start_date->format('Y-m-d') : '') }}" class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            @error('start_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="mb-8">
    <div class="grid grid-cols-2 gap-6">
        <div>
            <label for="current_mileage" class="block text-sm font-medium text-gray-700 mb-2">Quilometragem Atual</label>
            <input type="number" step="0.01" id="current_mileage" name="current_mileage" value="{{ old('current_mileage', $route->current_mileage ?? '') }}" class="w-full h-12 px-4 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
            @error('current_mileage')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
