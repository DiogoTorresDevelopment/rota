<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use App\Services\TruckService;
use App\Http\Requests\TruckRequest;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    protected $truckService;

    public function __construct(TruckService $truckService)
    {
        $this->truckService = $truckService;
    }

    public function index()
    {
        $trucks = Truck::all();
        return view('trucks.index', compact('trucks'));
    }

    public function create()
    {
        return view('trucks.create');
    }

    public function store(TruckRequest $request)
    {
        try {
            $truck = $this->truckService->store($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Caminhão cadastrado com sucesso!',
                'truck' => $truck
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar os dados do caminhão',
                'errors' => ['general' => [$e->getMessage()]]
            ], 422);
        }
    }

    public function edit(Truck $truck)
    {
        return view('trucks.edit', compact('truck'));
    }

    public function update(TruckRequest $request, Truck $truck)
    {
        try {
            $this->truckService->update($truck, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Caminhão atualizado com sucesso!',
                'truck' => $truck
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar os dados do caminhão',
                'errors' => ['general' => [$e->getMessage()]]
            ], 422);
        }
    }

    public function destroy(Truck $truck)
    {
        try {
            \Log::info('Iniciando exclusão do caminhão: ' . $truck->id);
            
            // Usa o método delete() do SoftDeletes
            $truck->delete();
            
            \Log::info('Caminhão excluído com sucesso: ' . $truck->id);
            
            return response()->json([
                'success' => true,
                'message' => 'Caminhão excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir caminhão: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir caminhão: ' . $e->getMessage()
            ], 500);
        }
    }
} 