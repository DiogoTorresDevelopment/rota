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

    public function create()
    {
        $availableRoutes = $this->deliveryService->getAvailableRoutes();
        $drivers = \App\Models\Driver::where('status', true)->get();
        $trucks = \App\Models\Truck::where('status', true)->get();
        $carrocerias = \App\Models\Carroceria::whereNull('deleted_at')->get();
        return view('deliveries.create', compact('availableRoutes', 'drivers', 'trucks', 'carrocerias'));
    }

    public function store(StoreDeliveryRequest $request)
    {
        try {
            \Log::info('Iniciando rota', ['request' => $request->all()]);
            
            $delivery = $this->deliveryService->startDelivery(
                $request->route_id,
                $request->driver_id,
                $request->truck_id,
                $request->carroceria_ids
            );
            
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
            $delivery->load(['driver','truck','carrocerias','currentStop.routeStop','route.stops']);
            
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

    public function apiDriverDeliveries(Request $request)
    {
        $driver = $request->user()->driver;
        
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de motorista não encontrado'
            ], 404);
        }

        $deliveries = Delivery::whereHas('route', function($query) use ($driver) {
                $query->where('driver_id', $driver->id);
            })
            ->with(['route', 'stop'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'status' => $delivery->status,
                    'created_at' => $delivery->created_at,
                    'completed_at' => $delivery->completed_at,
                    'notes' => $delivery->notes,
                    'photo_proof' => $delivery->photo_proof,
                    'route' => [
                        'id' => $delivery->route->id,
                        'name' => $delivery->route->name,
                        'status' => $delivery->route->status,
                    ],
                    'stop' => $delivery->stop ? [
                        'id' => $delivery->stop->id,
                        'name' => $delivery->stop->name,
                        'order' => $delivery->stop->order,
                        'latitude' => $delivery->stop->latitude,
                        'longitude' => $delivery->stop->longitude,
                        'address' => [
                            'street' => $delivery->stop->street,
                            'number' => $delivery->stop->number,
                            'city' => $delivery->stop->city,
                            'state' => $delivery->stop->state,
                            'cep' => $delivery->stop->cep,
                        ]
                    ] : null
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'deliveries' => $deliveries
            ]
        ]);
    }

    public function apiDriverDeliveryDetails(Request $request, Delivery $delivery)
    {
        $driver = $request->user()->driver;
        
        if (!$driver || $delivery->driver_id !== $driver->id) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'delivery' => [
                    'id' => $delivery->id,
                    'status' => $delivery->status,
                    'created_at' => $delivery->created_at,
                    'completed_at' => $delivery->completed_at,
                    'notes' => $delivery->notes,
                    'photo_proof' => $delivery->photo_proof,
                    'route' => [
                        'id' => $delivery->route->id,
                        'name' => $delivery->route->name,
                        'status' => $delivery->route->status,
                    ],
                    'stop' => $delivery->stop ? [
                        'id' => $delivery->stop->id,
                        'name' => $delivery->stop->name,
                        'order' => $delivery->stop->order,
                        'latitude' => $delivery->stop->latitude,
                        'longitude' => $delivery->stop->longitude,
                        'address' => [
                            'street' => $delivery->stop->street,
                            'number' => $delivery->stop->number,
                            'city' => $delivery->stop->city,
                            'state' => $delivery->stop->state,
                            'cep' => $delivery->stop->cep,
                        ]
                    ] : null
                ]
            ]
        ]);
    }

    public function apiCompleteDelivery(Request $request, Delivery $delivery)
    {
        $driver = $request->user()->driver;
        
        if (!$driver || $delivery->driver_id !== $driver->id) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:completed,cancelled',
            'notes' => 'nullable|string|max:1000',
            'photo_proof' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo_proof')) {
            $path = $request->file('photo_proof')->store('delivery-proofs', 'public');
            $validated['photo_proof'] = $path;
        }

        $delivery->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status da entrega atualizado com sucesso',
            'data' => [
                'delivery' => $delivery->fresh()
            ]
        ]);
    }

    public function changeResources(Request $request, Delivery $delivery)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'truck_id' => 'required|exists:trucks,id',
            'carroceria_ids' => 'required|array|min:1',
            'carroceria_ids.*' => 'exists:carrocerias,id',
        ]);

        $delivery = $this->deliveryService->changeResources($delivery, $validated['driver_id'], $validated['truck_id'], $validated['carroceria_ids']);

        return response()->json(['success' => true, 'data' => new DeliveryResource($delivery)]);
    }

    public function completeStop(Delivery $delivery)
    {
        $delivery = $this->deliveryService->completeCurrentStop($delivery);
        return response()->json(['success' => true, 'data' => new DeliveryResource($delivery)]);
    }

    public function cancel(Delivery $delivery)
    {
        $delivery = $this->deliveryService->cancelDelivery($delivery);
        return response()->json(['success' => true, 'data' => new DeliveryResource($delivery)]);
    }

    public function history(Delivery $delivery)
    {
        $history = $this->deliveryService->getHistory($delivery);
        return response()->json(['success' => true, 'data' => $history]);
    }
}
