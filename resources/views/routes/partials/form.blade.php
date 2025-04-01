<form id="route-form" action="{{ $action }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    
    <input type="hidden" name="step" value="{{ $currentStep }}">
    <input type="hidden" name="route_id" value="{{ $route->id ?? '' }}">

    {{-- Cabeçalho com Título --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Edição' : 'Cadastro' }} de Rota</h2>
        <p class="mt-1 text-sm text-gray-600">Preencha as informações para {{ $isEdit ? 'editar' : 'criar' }} uma rota.</p>
    </div>

    {{-- Indicador de Etapas --}}
    <div class="w-full mb-8">
        <div class="flex items-center justify-between">
            {{-- Etapa 1: Informações --}}
            <div class="flex flex-col items-center flex-1">
                <div class="flex items-center justify-center w-8 h-8 {{ $currentStep >= 1 ? 'bg-blue-600' : 'bg-gray-200' }} rounded-full">
                    <span class="text-sm font-medium text-white">1</span>
                </div>
                <div class="mt-2 text-center">
                    <span class="text-sm font-medium {{ $currentStep >= 1 ? 'text-blue-600' : 'text-gray-500' }}">Informações</span>
                </div>
            </div>

            {{-- Linha conectora --}}
            <div class="flex-1 h-1 mx-4 {{ $currentStep >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

            {{-- Etapa 2: Endereços --}}
            <div class="flex flex-col items-center flex-1">
                <div class="flex items-center justify-center w-8 h-8 {{ $currentStep >= 2 ? 'bg-blue-600' : 'bg-gray-200' }} rounded-full">
                    <span class="text-sm font-medium text-white">2</span>
                </div>
                <div class="mt-2 text-center">
                    <span class="text-sm font-medium {{ $currentStep >= 2 ? 'text-blue-600' : 'text-gray-500' }}">Endereços</span>
                </div>
            </div>

            {{-- Linha conectora --}}
            <div class="flex-1 h-1 mx-4 {{ $currentStep >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

            {{-- Etapa 3: Destinos --}}
            <div class="flex flex-col items-center flex-1">
                <div class="flex items-center justify-center w-8 h-8 {{ $currentStep >= 3 ? 'bg-blue-600' : 'bg-gray-200' }} rounded-full">
                    <span class="text-sm font-medium text-white">3</span>
                </div>
                <div class="mt-2 text-center">
                    <span class="text-sm font-medium {{ $currentStep >= 3 ? 'text-blue-600' : 'text-gray-500' }}">Destinos</span>
                </div>
            </div>
        </div>
    </div>

    

    {{-- Conteúdo das Etapas --}}
    <div class="bg-white rounded-lg shadow-sm p-8">

        {{-- Subtítulo da etapa atual --}}{{-- Subtítulo da etapa atual --}}
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900">
                @if($currentStep == 1)
                    Dados da rota
                @elseif($currentStep == 2)
                    Destinos da Rota
                @else
                    Destinos Intermediários
                @endif
            </h3>
            <p class="text-sm text-gray-600">
                @if($currentStep == 1)
                    Estas informações serão utilizadas dentro do sistema.
                @elseif($currentStep == 2)
                    Cadastre os destino padrão dessa rota.
                @else
                    Cadastre os destinos intermediários dessa rota.
                @endif
            </p>
        </div>

        @if($currentStep == 1)
            @include('routes.partials.step1', ['route' => $route ?? null])
        @elseif($currentStep == 2)
            @include('routes.partials.step2', ['route' => $route ?? null])
        @elseif($currentStep == 3)
            @include('routes.partials.step3', ['route' => $route ?? null])
        @endif

        {{-- Botões de Navegação --}}
        <div class="flex justify-between space-x-4 pt-8 border-t border-gray-200 mt-5">
            <div>
                @if($currentStep > 1)
                    <button type="button" 
                            onclick="window.location.href='{{ route('routes.' . ($isEdit ? 'edit' : 'create'), ['route' => $route->id ?? '', 'step' => $currentStep - 1]) }}'"
                            class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Voltar
                    </button>
                @endif
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('routes.index') }}" 
                   class="px-6 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-3 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700">
                    {{ $currentStep < 3 ? 'Avançar' : 'Finalizar' }}
                </button>
            </div>
        </div>
    </div>
</form>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('route-form');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch(this.action, {
                method: this.getAttribute('method'),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                
                // Redireciona para a próxima etapa
                const nextStep = {{ $currentStep }} + 1;
                if (nextStep <= 3) {
                    const redirectUrl = '{{ route('routes.edit', ':id') }}'.replace(':id', data.route_id) + '?step=' + nextStep;
                    console.log('Redirecionando para:', redirectUrl);
                    window.location.href = redirectUrl;
                } else {
                    window.location.href = '{{ route('routes.index') }}';
                }
            } else {
                throw new Error(data.message || 'Erro ao salvar os dados');
            }
        } catch (error) {
            console.error('Erro:', error);
           
        }
    });
});
</script>
@endpush 