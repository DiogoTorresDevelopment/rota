<?php

namespace App\Modules\Passengers\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Passengers\Services\PassengerService;
use App\Http\Requests\PassengerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PassengerController extends Controller
{
    public function __construct(
        private PassengerService $passengerService
    ) {}

    public function index(): View
    {
        $passengers = $this->passengerService->getAllPassengers();
        return view('passengers.index', compact('passengers'));
    }

    public function create(): View
    {
        return view('passengers.create');
    }

    public function store(PassengerRequest $request): JsonResponse
    {
        try {
            $passenger = $this->passengerService->createPassenger($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Passageiro cadastrado com sucesso!',
                'passenger' => $passenger
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar passageiro',
                'errors' => ['general' => [$e->getMessage()]]
            ], 422);
        }
    }

    public function show(int $id): View|RedirectResponse
    {
        $passenger = $this->passengerService->getPassengerById($id);
        
        if (!$passenger) {
            return redirect()->route('passengers.index')
                ->with('error', 'Passageiro não encontrado');
        }

        return view('passengers.show', compact('passenger'));
    }

    public function edit(int $id): View|RedirectResponse
    {
        $passenger = $this->passengerService->getPassengerById($id);
        
        if (!$passenger) {
            return redirect()->route('passengers.index')
                ->with('error', 'Passageiro não encontrado');
        }

        return view('passengers.edit', compact('passenger'));
    }

    public function update(PassengerRequest $request, int $id): JsonResponse
    {
        try {
            $passenger = $this->passengerService->getPassengerById($id);
            
            if (!$passenger) {
                return response()->json([
                    'success' => false,
                    'message' => 'Passageiro não encontrado'
                ], 404);
            }

            $updatedPassenger = $this->passengerService->updatePassenger($passenger, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Passageiro atualizado com sucesso!',
                'passenger' => $updatedPassenger
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar passageiro',
                'errors' => ['general' => [$e->getMessage()]]
            ], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $passenger = $this->passengerService->getPassengerById($id);
            
            if (!$passenger) {
                return response()->json([
                    'success' => false,
                    'message' => 'Passageiro não encontrado'
                ], 404);
            }

            $this->passengerService->deletePassenger($passenger);

            return response()->json([
                'success' => true,
                'message' => 'Passageiro excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir passageiro: ' . $e->getMessage()
            ], 500);
        }
    }
}