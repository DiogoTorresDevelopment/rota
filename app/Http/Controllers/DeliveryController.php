<?php

namespace App\Http\Controllers;

use App\Http\Requests\Delivery\StoreDeliveryRequest;
use App\Http\Requests\Delivery\CompleteDeliveryRequest;
use App\Http\Resources\DeliveryResource;
use App\Services\DeliveryService;
use App\Models\Delivery;
use App\Models\Route;
use Illuminate\Http\Request;
use Exception;

class DeliveryController extends Controller
{
    protected $deliveryService;

    public function __construct(DeliveryService $deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    public function index()
    {
        $deliveries = $this->deliveryService->getDeliveries();
        $availableRoutes = $this->deliveryService->getAvailableRoutes();

        return view('deliveries.index', compact('deliveries', 'availableRoutes'));
    }

    public function store(StoreDeliveryRequest $request)
    {
        try {
            \Log::info('Iniciando rota', ['request' => $request->all()]);
            
            $delivery = $this->deliveryService->startDelivery($request->route_id);
            
            \Log::info('Rota iniciada com sucesso', ['delivery' => $delivery]);

            return response()->json([
                'success' => true,
                'message' => 'Rota iniciada com sucesso!',
                'data' => new DeliveryResource($delivery)
            ]);

        } catch (Exception $e) {
            \Log::error('Erro ao iniciar rota', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar rota: ' . $e->getMessage()
            ], 422);
        }
    }

    public function complete(CompleteDeliveryRequest $request, Delivery $delivery)
    {
        try {
            \Log::info('Finalizando entrega', ['delivery_id' => $delivery->id]);
            
            $delivery = $this->deliveryService->completeDelivery($delivery);
            
            \Log::info('Entrega finalizada com sucesso', ['delivery' => $delivery]);

            return response()->json([
                'success' => true,
                'message' => 'Entrega finalizada com sucesso!',
                'data' => new DeliveryResource($delivery)
            ]);

        } catch (Exception $e) {
            \Log::error('Erro ao finalizar entrega', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar entrega: ' . $e->getMessage()
            ], 422);
        }
    }

    public function details(Delivery $delivery)
    {
        try {
            $delivery->load(['route.driver', 'route.stops']);
            
            return response()->json([
                'success' => true,
                'data' => new DeliveryResource($delivery)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar detalhes: ' . $e->getMessage()
            ], 422);
        }
    }

    public function reuse(Delivery $delivery)
    {
        try {
            $newDelivery = $this->deliveryService->reuseRoute($delivery);
            
            return response()->json([
                'success' => true,
                'message' => 'Nova entrega iniciada com sucesso!',
                'data' => new DeliveryResource($newDelivery)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao reutilizar rota: ' . $e->getMessage()
            ], 422);
        }
    }
} 