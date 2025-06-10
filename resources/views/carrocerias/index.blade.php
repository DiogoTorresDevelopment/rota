@extends('layout.master')

@section('content')
<div class="h-full flex flex-col">
  <div class="flex justify-between items-center mb-6">
    <div>
      <h1 class="text-xl font-semibold text-gray-900">Carrocerias</h1>
    </div>
    <a href="{{ route('carrocerias.create') }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg">Adicionar</a>
  </div>

  <div class="bg-white rounded-lg shadow-sm">
    <div class="p-6">
      <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-xs uppercase">
          <tr>
            <th class="px-6 py-3">Descrição</th>
            <th class="px-6 py-3">Chassi</th>
            <th class="px-6 py-3">Placa</th>
            <th class="px-6 py-3">Peso Suportado</th>
            <th class="px-6 py-3">Status</th>
            <th class="px-6 py-3 text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($carrocerias as $carroceria)
          <tr>
            <td class="px-6 py-4">{{ $carroceria->descricao }}</td>
            <td class="px-6 py-4">{{ $carroceria->chassi }}</td>
            <td class="px-6 py-4">{{ $carroceria->placa }}</td>
            <td class="px-6 py-4">{{ $carroceria->peso_suportado }}</td>
            <td class="px-6 py-4">{{ $carroceria->status ? 'Ativa' : 'Inativa' }}</td>
            <td class="px-6 py-4 text-right">
              <a href="{{ route('carrocerias.edit', $carroceria) }}" class="text-blue-600">Editar</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

    </div>
  </div>
</div>
@endsection


@push('custom-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown actions
    const dropdowns = document.querySelectorAll('[data-dropdown-toggle]');
    function closeAllDropdowns(exceptId = null) {
        dropdowns.forEach(dropdown => {
            const dropdownId = dropdown.getAttribute('data-dropdown-toggle');
            if (dropdownId !== exceptId) {
                const dropdownMenu = document.getElementById(dropdownId);
                if (dropdownMenu) {
                    dropdownMenu.classList.add('hidden');
                }
            }
        });
    }
    dropdowns.forEach(dropdown => {
        const dropdownId = dropdown.getAttribute('data-dropdown-toggle');
        const dropdownMenu = document.getElementById(dropdownId);
        if (dropdownMenu) {
            dropdown.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeAllDropdowns(dropdownId);
                dropdownMenu.classList.toggle('hidden');
            });
        }
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown-toggle]')) {
            closeAllDropdowns();
        }
    });
    // Confirmação de exclusão com SweetAlert2
    const deleteButtons = document.querySelectorAll('.delete-carroceria');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
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
                    fetch(form.action, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: 'Carroceria excluída com sucesso!',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Erro ao excluir a carroceria');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Erro!',
                            text: error.message,
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    });
                }
            });
        });
    });
});
</script>
@endpush
