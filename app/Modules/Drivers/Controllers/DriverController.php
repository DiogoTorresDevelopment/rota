<?php

namespace App\Modules\Drivers\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Drivers\Services\DriverService;
use App\Http\Requests\DriverRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function __construct(
        private DriverService $driverService
    ) {}

    public function index(): View
    {
        $drivers = $this->driverService->getAllDrivers();
        return view('drivers.index', compact('drivers'));
    }

    public function create(): View
    {
        return view('drivers.create');
    }

    public function store(DriverRequest $request): JsonResponse
    {
        try {
            $driver = $this->driverService->createDriver($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Motorista cadastrado com sucesso!',
                'driver' => $driver
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cadastrar motorista',
                'errors' => ['general' => [$e->getMessage()]]
            ], 422);
        }
    }

    public function show(int $id): View|RedirectResponse
    {
        $driver = $this->driverService->getDriverById($id);
        
        if (!$driver) {
            return redirect()->route('drivers.index')
                ->with('error', 'Motorista não encontrado');
        }

        return view('drivers.show', compact('driver'));
    }

    public function edit(int $id): View|RedirectResponse
    {
        $driver = $this->driverService->getDriverById($id);
        
        if (!$driver) {
            return redirect()->route('drivers.index')
                ->with('error', 'Motorista não encontrado');
        }

        return view('drivers.edit', compact('driver'));
    }

    public function update(DriverRequest $request, int $id): JsonResponse
    {
        try {
            $driver = $this->driverService->getDriverById($id);
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motorista não encontrado'
                ], 404);
            }

            $updatedDriver = $this->driverService->updateDriver($driver, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Motorista atualizado com sucesso!',
                'driver' => $updatedDriver
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar motorista',
                'errors' => ['general' => [$e->getMessage()]]
            ], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $driver = $this->driverService->getDriverById($id);
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Motorista não encontrado'
                ], 404);
            }

            $this->driverService->deleteDriver($driver);

            return response()->json([
                'success' => true,
                'message' => 'Motorista excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir motorista: ' . $e->getMessage()
            ], 500);
        }
    }
}