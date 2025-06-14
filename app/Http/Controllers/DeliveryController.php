<?php

namespace App\Http\Controllers;

use App\Http\Requests\Delivery\StoreDeliveryRequest;
use App\Http\Requests\Delivery\CompleteDeliveryRequest;
use App\Http\Resources\DeliveryResource;
use App\Services\DeliveryService;
use App\Models\Delivery;
use App\Models\Route;
use App\Models\DeliveryStop;
use App\Models\DeliveryStopPhoto;
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
        $availableDrivers = $this->deliveryService->getAvailableDrivers();
        $availableTrucks = $this->deliveryService->getAvailableTrucks();
        $availableCarrocerias = $this->deliveryService->getAvailableCarrocerias();
        return view('deliveries.index', compact('deliveries', 'availableRoutes', 'availableDrivers', 'availableTrucks', 'availableCarrocerias'));
    }

    public function create()
    {
        $availableRoutes = $this->deliveryService->getAvailableRoutes();
        $availableDrivers = $this->deliveryService->getAvailableDrivers();
        $availableTrucks = $this->deliveryService->getAvailableTrucks();
        $availableCarrocerias = $this->deliveryService->getAvailableCarrocerias();
        return view('deliveries.create', compact('availableRoutes', 'availableDrivers', 'availableTrucks', 'availableCarrocerias'));
    }

    public function store(StoreDeliveryRequest $request)
    {
        try {
            \Log::info('Iniciando rota', ['request' => $request->all()]);
            
            $delivery = $this->deliveryService->startDelivery(
                $request->route_id,
                $request->driver_id,
                $request->truck_id,
                $request->carroceria_ids,
                $request->start_date,
                $request->end_date
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
            $delivery->load([
                'deliveryRoute.stops',
                'deliveryDriver',
                'deliveryTruck',
                'deliveryCarrocerias',
                'deliveryStops.deliveryRouteStop'
            ]);

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

        $deliveries = Delivery::where('original_driver_id', $driver->id)
            ->with([
                'deliveryRoute',
                'deliveryStops.deliveryRouteStop'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'status' => $delivery->status,
                    'created_at' => $delivery->created_at,
                    'completed_at' => $delivery->end_date,
                    'notes' => $delivery->notes,
                    'route' => [
                        'id' => $delivery->original_route_id,
                        'name' => $delivery->deliveryRoute->name,
                        'status' => $delivery->deliveryRoute->status,
                    ],
                    'current_stop' => $delivery->currentStop ? [
                        'id' => $delivery->currentStop->id,
                        'name' => $delivery->currentStop->deliveryRouteStop->name,
                        'order' => $delivery->currentStop->order,
                        'latitude' => $delivery->currentStop->deliveryRouteStop->latitude,
                        'longitude' => $delivery->currentStop->deliveryRouteStop->longitude,
                        'address' => [
                            'street' => $delivery->currentStop->deliveryRouteStop->street,
                            'number' => $delivery->currentStop->deliveryRouteStop->number,
                            'city' => $delivery->currentStop->deliveryRouteStop->city,
                            'state' => $delivery->currentStop->deliveryRouteStop->state,
                            'cep' => $delivery->currentStop->deliveryRouteStop->cep,
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
        
        if (!$driver || $delivery->original_driver_id !== $driver->id) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada'
            ], 404);
        }

        $delivery->load([
            'deliveryRoute',
            'deliveryDriver',
            'deliveryTruck',
            'deliveryCarrocerias',
            'deliveryStops.deliveryRouteStop'
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'delivery' => [
                    'id' => $delivery->id,
                    'status' => $delivery->status,
                    'created_at' => $delivery->created_at,
                    'completed_at' => $delivery->end_date,
                    'notes' => $delivery->notes,
                    'route' => [
                        'id' => $delivery->original_route_id,
                        'name' => $delivery->deliveryRoute->name,
                        'status' => $delivery->deliveryRoute->status,
                    ],
                    'driver' => [
                        'id' => $delivery->original_driver_id,
                        'name' => $delivery->deliveryDriver->name,
                        'cpf' => $delivery->deliveryDriver->cpf,
                        'phone' => $delivery->deliveryDriver->phone,
                        'email' => $delivery->deliveryDriver->email,
                        'cep' => $delivery->deliveryDriver->cep,
                        'state' => $delivery->deliveryDriver->state,
                        'city' => $delivery->deliveryDriver->city,
                        'street' => $delivery->deliveryDriver->street,
                        'number' => $delivery->deliveryDriver->number,
                    ],
                    'truck' => [
                        'id' => $delivery->original_truck_id,
                        'marca' => $delivery->deliveryTruck->marca,
                        'modelo' => $delivery->deliveryTruck->modelo,
                        'placa' => $delivery->deliveryTruck->placa,
                        'chassi' => $delivery->deliveryTruck->chassi,
                        'ano' => $delivery->deliveryTruck->ano,
                        'cor' => $delivery->deliveryTruck->cor,
                        'tipo_combustivel' => $delivery->deliveryTruck->tipo_combustivel,
                        'carga_suportada' => $delivery->deliveryTruck->carga_suportada,
                        'quilometragem' => $delivery->deliveryTruck->quilometragem,
                        'ultima_revisao' => $delivery->deliveryTruck->ultima_revisao,
                    ],
                    'carrocerias' => $delivery->deliveryCarrocerias->map(function($c) {
                        return [
                            'id' => $c->id,
                            'descricao' => $c->descricao,
                            'placa' => $c->placa,
                        ];
                    }),
                    'current_stop' => $delivery->currentStop ? [
                        'id' => $delivery->currentStop->id,
                        'name' => $delivery->currentStop->deliveryRouteStop->name,
                        'order' => $delivery->currentStop->order,
                        'latitude' => $delivery->currentStop->deliveryRouteStop->latitude,
                        'longitude' => $delivery->currentStop->deliveryRouteStop->longitude,
                        'address' => [
                            'street' => $delivery->currentStop->deliveryRouteStop->street,
                            'number' => $delivery->currentStop->deliveryRouteStop->number,
                            'city' => $delivery->currentStop->deliveryRouteStop->city,
                            'state' => $delivery->currentStop->deliveryRouteStop->state,
                            'cep' => $delivery->currentStop->deliveryRouteStop->cep,
                        ]
                    ] : null
                ]
            ]
        ]);
    }

    public function apiCompleteDelivery(Request $request, Delivery $delivery)
    {
        $driver = $request->user()->driver;
        
        if (!$driver || $delivery->original_driver_id !== $driver->id) {
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
        try {
            $validated = $request->validate([
                'route_id' => 'required|exists:routes,id',
                'driver_id' => 'required|exists:drivers,id',
                'truck_id' => 'required|exists:trucks,id',
                'carroceria_ids' => 'required|array|min:1',
                'carroceria_ids.*' => 'exists:carrocerias,id',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date'
            ]);

            $delivery = $this->deliveryService->changeResources(
                $delivery,
                $validated['route_id'],
                $validated['driver_id'],
                $validated['truck_id'],
                $validated['carroceria_ids'],
                $validated['start_date'],
                $validated['end_date']
            );

            return response()->json([
                'success' => true,
                'message' => 'Entrega atualizada com sucesso!',
                'data' => new DeliveryResource($delivery)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar entrega: ' . $e->getMessage()
            ], 422);
        }
    }

    public function completeStop(Delivery $delivery)
    {
        try {
            $delivery = $this->deliveryService->completeCurrentStop($delivery);
            return response()->json([
                'success' => true,
                'message' => 'Parada concluída com sucesso!',
                'data' => new DeliveryResource($delivery)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao concluir parada: ' . $e->getMessage()
            ], 422);
        }
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

    public function show(Delivery $delivery)
    {
        $delivery->load([
            'deliveryRoute.stops',
            'deliveryDriver',
            'deliveryTruck',
            'deliveryCarrocerias',
            'deliveryStops.deliveryRouteStop'
        ]);
        return view('deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery)
    {
        $currentStep = request()->get('step', 1);
        
        $delivery->load([
            'deliveryRoute.stops',
            'deliveryDriver',
            'deliveryTruck',
            'deliveryCarrocerias',
            'deliveryStops.deliveryRouteStop'
        ]);

        // Carrega recursos disponíveis para edição
        $availableRoutes = $this->deliveryService->getAvailableRoutes();
        $availableDrivers = $this->deliveryService->getAvailableDrivers();
        $availableTrucks = $this->deliveryService->getAvailableTrucks();
        $availableCarrocerias = $this->deliveryService->getAvailableCarrocerias();

        return view('deliveries.edit', compact(
            'delivery', 
            'currentStep',
            'availableRoutes',
            'availableDrivers',
            'availableTrucks',
            'availableCarrocerias'
        ));
    }

    public function editStop(Delivery $delivery, DeliveryStop $stop)
    {
        // Verifica se a parada pertence à entrega
        if ($stop->delivery_id !== $delivery->id) {
            abort(404);
        }

        $stop->load('deliveryRouteStop');
        
        return view('deliveries.edit-stop', compact('delivery', 'stop'));
    }

    public function updateStop(Request $request, Delivery $delivery, DeliveryStop $stop)
    {
        try {
            // Verifica se a parada pertence à entrega
            if ($stop->delivery_id !== $delivery->id) {
                throw new Exception('Parada não encontrada');
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,completed,cancelled',
                'notes' => 'nullable|string|max:1000',
                'completed_at' => 'nullable|date',
                'photos.*' => 'nullable|image|max:2048'
            ]);

            // Upload das fotos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('delivery-proofs', 'public');
                    
                    DeliveryStopPhoto::create([
                        'delivery_stop_id' => $stop->id,
                        'path' => $path,
                        'original_name' => $photo->getClientOriginalName(),
                        'mime_type' => $photo->getMimeType(),
                        'size' => $photo->getSize()
                    ]);
                }
            }

            // Atualiza a parada
            $stop->update([
                'status' => $validated['status'],
                'notes' => $validated['notes'],
                'completed_at' => $validated['completed_at']
            ]);

            // Se a parada foi concluída, atualiza a próxima parada atual
            if ($validated['status'] === 'completed' && $stop->id === $delivery->current_delivery_stop_id) {
                $nextStop = $delivery->deliveryStops()
                    ->where('order', '>', $stop->order)
                    ->orderBy('order')
                    ->first();

                if ($nextStop) {
                    $delivery->update(['current_delivery_stop_id' => $nextStop->id]);
                } else {
                    // Se não há próxima parada, finaliza a entrega
                    $this->deliveryService->completeDelivery($delivery);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Parada atualizada com sucesso!',
                'data' => [
                    'stop' => $stop->fresh(['deliveryRouteStop', 'photos']),
                    'delivery' => $delivery->fresh()
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar parada: ' . $e->getMessage()
            ], 422);
        }
    }

    public function historyView(Delivery $delivery)
    {
        $delivery->load(['deliveryRoute', 'deliveryDriver', 'deliveryTruck']);
        return view('deliveries.history', compact('delivery'));
    }
}
