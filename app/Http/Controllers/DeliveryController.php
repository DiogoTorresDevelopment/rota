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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        try {
            $driver = $request->user();
            
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perfil de motorista não encontrado'
                ], 404);
            }

            $deliveries = Delivery::where('original_driver_id', $driver->id)
                ->with([
                    'deliveryRoute',
                    'deliveryStops.deliveryRouteStop',
                    'deliveryTruck',
                    'deliveryCarrocerias.carroceria',
                    'currentStop.deliveryRouteStop'
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
                        'truck' => $delivery->deliveryTruck ? [
                            'id' => $delivery->deliveryTruck->id,
                            'plate' => $delivery->deliveryTruck->plate,
                            'model' => $delivery->deliveryTruck->model,
                            'carrocerias' => $delivery->deliveryCarrocerias->map(function($deliveryCarroceria) {
                                return [
                                    'id' => $deliveryCarroceria->carroceria->id,
                                    'name' => $deliveryCarroceria->carroceria->name,
                                    'type' => $deliveryCarroceria->carroceria->type,
                                    'description' => $deliveryCarroceria->descricao,
                                    'plate' => $deliveryCarroceria->placa,
                                    'chassi' => $deliveryCarroceria->chassi,
                                    'peso_suportado' => $deliveryCarroceria->peso_suportado
                                ];
                            })
                        ] : null,
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
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar entregas do motorista', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar entregas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiDriverDeliveryDetails(Request $request, Delivery $delivery)
    {
        try {
            $driver = $request->user();
            
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
                'deliveryCarrocerias.carroceria',
                'deliveryStops.deliveryRouteStop',
                'currentStop.deliveryRouteStop'
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
                            'phone' => $delivery->deliveryDriver->phone,
                            'email' => $delivery->deliveryDriver->email,
                        ],
                        'truck' => $delivery->deliveryTruck ? [
                            'id' => $delivery->deliveryTruck->id,
                            'plate' => $delivery->deliveryTruck->plate,
                            'model' => $delivery->deliveryTruck->model,
                            'carrocerias' => $delivery->deliveryCarrocerias->map(function($deliveryCarroceria) {
                                return [
                                    'id' => $deliveryCarroceria->carroceria->id,
                                    'name' => $deliveryCarroceria->carroceria->name,
                                    'type' => $deliveryCarroceria->carroceria->type,
                                    'description' => $deliveryCarroceria->descricao,
                                    'plate' => $deliveryCarroceria->placa,
                                    'chassi' => $deliveryCarroceria->chassi,
                                    'peso_suportado' => $deliveryCarroceria->peso_suportado
                                ];
                            })
                        ] : null,
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
                        ] : null,
                        'stops' => $delivery->deliveryStops->map(function ($stop) {
                            return [
                                'id' => $stop->id,
                                'name' => $stop->deliveryRouteStop->name,
                                'order' => $stop->order,
                                'status' => $stop->status,
                                'completed_at' => $stop->completed_at,
                                'latitude' => $stop->deliveryRouteStop->latitude,
                                'longitude' => $stop->deliveryRouteStop->longitude,
                                'address' => [
                                    'street' => $stop->deliveryRouteStop->street,
                                    'number' => $stop->deliveryRouteStop->number,
                                    'city' => $stop->deliveryRouteStop->city,
                                    'state' => $stop->deliveryRouteStop->state,
                                    'cep' => $stop->deliveryRouteStop->cep,
                                ]
                            ];
                        })
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar detalhes da entrega', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar detalhes da entrega: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiCompleteDelivery(Request $request, Delivery $delivery)
    {
        try {
            $driver = $request->user();
            
            if (!$driver || $delivery->original_driver_id !== $driver->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entrega não encontrada'
                ], 404);
            }

            if ($delivery->status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta entrega já foi finalizada'
                ], 400);
            }

            $validated = $request->validate([
                'notes' => 'nullable|string|max:1000'
            ]);

            $delivery = $this->deliveryService->completeDelivery($delivery);

            if ($validated['notes']) {
                $delivery->update([
                    'notes' => $validated['notes']
                ]);
            }

            $delivery->load([
                'deliveryRoute',
                'deliveryDriver',
                'deliveryTruck',
                'deliveryCarrocerias.carroceria',
                'deliveryStops.deliveryRouteStop',
                'currentStop.deliveryRouteStop'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entrega finalizada com sucesso',
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
                            'phone' => $delivery->deliveryDriver->phone,
                            'email' => $delivery->deliveryDriver->email,
                        ],
                        'truck' => $delivery->deliveryTruck ? [
                            'id' => $delivery->deliveryTruck->id,
                            'plate' => $delivery->deliveryTruck->plate,
                            'model' => $delivery->deliveryTruck->model,
                            'carrocerias' => $delivery->deliveryCarrocerias->map(function($deliveryCarroceria) {
                                return [
                                    'id' => $deliveryCarroceria->carroceria->id,
                                    'name' => $deliveryCarroceria->carroceria->name,
                                    'type' => $deliveryCarroceria->carroceria->type,
                                    'description' => $deliveryCarroceria->descricao,
                                    'plate' => $deliveryCarroceria->placa,
                                    'chassi' => $deliveryCarroceria->chassi,
                                    'peso_suportado' => $deliveryCarroceria->peso_suportado
                                ];
                            })
                        ] : null,
                        'stops' => $delivery->deliveryStops->map(function ($stop) {
                            return [
                                'id' => $stop->id,
                                'name' => $stop->deliveryRouteStop->name,
                                'order' => $stop->order,
                                'status' => $stop->status,
                                'completed_at' => $stop->completed_at,
                                'photos' => $stop->photos->map(function($photo) {
                                    return [
                                        'id' => $photo->id,
                                        'url' => $photo->url,
                                        'created_at' => $photo->created_at
                                    ];
                                }),
                                'latitude' => $stop->deliveryRouteStop->latitude,
                                'longitude' => $stop->deliveryRouteStop->longitude,
                                'address' => [
                                    'street' => $stop->deliveryRouteStop->street,
                                    'number' => $stop->deliveryRouteStop->number,
                                    'city' => $stop->deliveryRouteStop->city,
                                    'state' => $stop->deliveryRouteStop->state,
                                    'cep' => $stop->deliveryRouteStop->cep,
                                ]
                            ];
                        })
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao finalizar entrega', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao finalizar entrega: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiCancelDelivery(Request $request, Delivery $delivery)
    {
        $driver = $request->user();
        
        if (!$driver || $delivery->original_driver_id !== $driver->id) {
            return response()->json([
                'success' => false,
                'message' => 'Entrega não encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
            'photo_proof' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo_proof')) {
            $path = $request->file('photo_proof')->store('delivery-proofs', 'public');
            $validated['photo_proof'] = $path;
        }

        $delivery->update([
            'status' => 'cancelled',
            'notes' => $validated['notes'],
            'photo_proof' => $validated['photo_proof'] ?? null,
            'end_date' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entrega cancelada com sucesso',
            'delivery' => $delivery->fresh()
        ]);
    }

    public function changeResources(Request $request, Delivery $delivery)
    {
        try {
            $validated = $request->validate([
                'route_id' => 'nullable|exists:routes,id',
                'driver_id' => 'nullable|exists:drivers,id',
                'truck_id' => 'nullable|exists:trucks,id',
                'carroceria_ids' => 'nullable|array|min:1',
                'carroceria_ids.*' => 'exists:carrocerias,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date'
            ]);

            $delivery = $this->deliveryService->changeResources(
                $delivery,
                $validated['route_id'] ?? null,
                $validated['driver_id'] ?? null,
                $validated['truck_id'] ?? null,
                $validated['carroceria_ids'] ?? null,
                $validated['start_date'] ?? null,
                $validated['end_date'] ?? null
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

    public function apiCompleteStop(Request $request, Delivery $delivery)
    {
        try {
            $driver = $request->user();
            
            if (!$driver || $delivery->original_driver_id !== $driver->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entrega não encontrada'
                ], 404);
            }

            if (!$delivery->currentStop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não há parada atual para concluir'
                ], 400);
            }

            $validated = $request->validate([
                'photos' => 'nullable|array',
                'photos.*' => 'image|max:2048'
            ]);

            $delivery = $this->deliveryService->completeCurrentStop($delivery);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('stop-proofs', 'public');
                    $delivery->currentStop->photos()->create([
                        'file_path' => $path,
                        'original_name' => $photo->getClientOriginalName()
                    ]);
                }
            }

            $delivery->load([
                'deliveryRoute',
                'deliveryDriver',
                'deliveryTruck',
                'deliveryCarrocerias.carroceria',
                'deliveryStops.deliveryRouteStop',
                'currentStop.deliveryRouteStop',
                'currentStop.photos'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Parada concluída com sucesso',
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
                            'phone' => $delivery->deliveryDriver->phone,
                            'email' => $delivery->deliveryDriver->email,
                        ],
                        'truck' => $delivery->deliveryTruck ? [
                            'id' => $delivery->deliveryTruck->id,
                            'plate' => $delivery->deliveryTruck->plate,
                            'model' => $delivery->deliveryTruck->model,
                            'carrocerias' => $delivery->deliveryCarrocerias->map(function($deliveryCarroceria) {
                                return [
                                    'id' => $deliveryCarroceria->carroceria->id,
                                    'name' => $deliveryCarroceria->carroceria->name,
                                    'type' => $deliveryCarroceria->carroceria->type,
                                    'description' => $deliveryCarroceria->descricao,
                                    'plate' => $deliveryCarroceria->placa,
                                    'chassi' => $deliveryCarroceria->chassi,
                                    'peso_suportado' => $deliveryCarroceria->peso_suportado
                                ];
                            })
                        ] : null,
                        'current_stop' => $delivery->currentStop ? [
                            'id' => $delivery->currentStop->id,
                            'name' => $delivery->currentStop->deliveryRouteStop->name,
                            'order' => $delivery->currentStop->order,
                            'status' => $delivery->currentStop->status,
                            'completed_at' => $delivery->currentStop->completed_at,
                            'photos' => $delivery->currentStop->photos->map(function($photo) {
                                return [
                                    'id' => $photo->id,
                                    'url' => $photo->url,
                                    'created_at' => $photo->created_at
                                ];
                            }),
                            'latitude' => $delivery->currentStop->deliveryRouteStop->latitude,
                            'longitude' => $delivery->currentStop->deliveryRouteStop->longitude,
                            'address' => [
                                'street' => $delivery->currentStop->deliveryRouteStop->street,
                                'number' => $delivery->currentStop->deliveryRouteStop->number,
                                'city' => $delivery->currentStop->deliveryRouteStop->city,
                                'state' => $delivery->currentStop->deliveryRouteStop->state,
                                'cep' => $delivery->currentStop->deliveryRouteStop->cep,
                            ]
                        ] : null,
                        'stops' => $delivery->deliveryStops->map(function ($stop) {
                            return [
                                'id' => $stop->id,
                                'name' => $stop->deliveryRouteStop->name,
                                'order' => $stop->order,
                                'status' => $stop->status,
                                'completed_at' => $stop->completed_at,
                                'photos' => $stop->photos->map(function($photo) {
                                    return [
                                        'id' => $photo->id,
                                        'url' => $photo->url,
                                        'created_at' => $photo->created_at
                                    ];
                                }),
                                'latitude' => $stop->deliveryRouteStop->latitude,
                                'longitude' => $stop->deliveryRouteStop->longitude,
                                'address' => [
                                    'street' => $stop->deliveryRouteStop->street,
                                    'number' => $stop->deliveryRouteStop->number,
                                    'city' => $stop->deliveryRouteStop->city,
                                    'state' => $stop->deliveryRouteStop->state,
                                    'cep' => $stop->deliveryRouteStop->cep,
                                ]
                            ];
                        })
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao concluir parada', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao concluir parada: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Delivery $delivery)
    {
        $delivery = $this->deliveryService->cancelDelivery($delivery);
        return response()->json(['success' => true, 'data' => new DeliveryResource($delivery)]);
    }

    public function history(Delivery $delivery)
    {
        try {
            // Carrega todos os dados necessários
            $delivery->load([
                'deliveryRoute',
                'deliveryDriver',
                'deliveryTruck',
                'deliveryCarrocerias',
                'deliveryStops' => function($query) {
                    $query->with(['deliveryRouteStop' => function($q) {
                        $q->with('deliveryRoute');
                    }])->orderBy('order');
                },
                'histories' => function($query) {
                    $query->with([
                        'deliveryStop.deliveryRouteStop',
                        'driver',
                        'truck',
                        'carrocerias'
                    ])->orderBy('created_at', 'desc');
                }
            ]);

            // Verifica se a rota existe
            if (!$delivery->deliveryRoute) {
                throw new Exception('Rota não encontrada para esta entrega');
            }

            // Verifica se há histórico
            if ($delivery->histories->isEmpty()) {
                \Log::warning('Nenhum histórico encontrado para a entrega', [
                    'delivery_id' => $delivery->id
                ]);
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'delivery' => $delivery
                    ]
                ]);
            }

            return view('deliveries.history', compact('delivery'));
        } catch (Exception $e) {
            \Log::error('Erro ao carregar histórico', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não foi possível carregar o histórico: ' . $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Não foi possível carregar o histórico: ' . $e->getMessage());
        }
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
        $availableDrivers = $this->deliveryService->getAvailableDrivers($delivery);
        $availableTrucks = $this->deliveryService->getAvailableTrucks($delivery);
        $availableCarrocerias = $this->deliveryService->getAvailableCarrocerias($delivery);

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
            $request->validate([
                'status' => 'required|in:pending,completed,cancelled',
                'completed_at' => 'nullable|date',
                'uploaded_photos' => 'nullable|json'
            ]);

            DB::beginTransaction();

            // Update delivery stop
            $stop->update([
                'status' => $request->status,
                'completed_at' => $request->completed_at
            ]);

            // Handle uploaded photos
            if ($request->has('uploaded_photos')) {
                $uploadedPhotos = json_decode($request->uploaded_photos, true);
                if (is_array($uploadedPhotos)) {
                    foreach ($uploadedPhotos as $photoId) {
                        $photo = DeliveryStopPhoto::find($photoId);
                        if ($photo) {
                            $photo->update(['delivery_stop_id' => $stop->id]);
                        }
                    }
                }
            }

            // Update current delivery stop if needed
            if ($request->status === 'completed' && $stop->order === $delivery->current_stop) {
                $nextStop = DeliveryStop::where('delivery_id', $delivery->id)
                    ->where('order', '>', $stop->order)
                    ->orderBy('order')
                    ->first();

                if ($nextStop) {
                    $delivery->update(['current_stop' => $nextStop->order]);
                } else {
                    $delivery->update(['current_stop' => null]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Parada atualizada com sucesso!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating stop: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar parada: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadPhoto(Request $request)
    {
        try {
            \Log::info('Iniciando upload de foto', [
                'request' => $request->all(),
                'has_file' => $request->hasFile('photo'),
                'file_valid' => $request->file('photo') ? $request->file('photo')->isValid() : false
            ]);

            if (!$request->hasFile('photo')) {
                throw new \Exception('Nenhum arquivo foi enviado');
            }

            $file = $request->file('photo');
            if (!$file->isValid()) {
                throw new \Exception('Arquivo inválido');
            }

            // Validação básica
            $request->validate([
                'photo' => 'required|image|max:2048', // max 2MB
                'delivery_id' => 'required|exists:deliveries,id',
                'stop_id' => 'required|exists:delivery_stops,id'
            ]);

            // Define o caminho base absoluto
            $basePath = storage_path('app/public/delivery-proofs');
            $deliveryPath = $basePath . '/' . $request->delivery_id;
            $stopPath = $deliveryPath . '/' . $request->stop_id;

            // Cria os diretórios se não existirem
            if (!file_exists($basePath)) {
                mkdir($basePath, 0775, true);
            }
            if (!file_exists($deliveryPath)) {
                mkdir($deliveryPath, 0775, true);
            }
            if (!file_exists($stopPath)) {
                mkdir($stopPath, 0775, true);
            }

            // Gera um nome único para o arquivo
            $fileName = uniqid() . '_' . $file->getClientOriginalName();
            $fullPath = $stopPath . '/' . $fileName;

            // Pegue as informações ANTES de mover
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();

            // Move o arquivo para o diretório
            $file->move($stopPath, $fileName);

            // Cria o registro no banco de dados
            $photo = DeliveryStopPhoto::create([
                'delivery_stop_id' => $request->stop_id,
                'path' => 'delivery-proofs/' . $request->delivery_id . '/' . $request->stop_id . '/' . $fileName,
                'original_name' => $originalName,
                'mime_type' => $mimeType,
                'size' => $size
            ]);

            \Log::info('Upload concluído com sucesso', [
                'photo_id' => $photo->id,
                'path' => $fullPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto enviada com sucesso',
                'photo_id' => $photo->id,
                'url' => Storage::url('delivery-proofs/' . $request->delivery_id . '/' . $request->stop_id . '/' . $fileName)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro no upload de foto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload da foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiUploadPhoto(Request $request)
    {
        return $this->uploadPhoto($request);
    }

    public function apiDeletePhoto(DeliveryStopPhoto $photo)
    {
        return $this->deletePhoto($photo);
    }

    public function deletePhoto(DeliveryStopPhoto $photo)
    {
        try {
            // Delete file from storage
            Storage::disk('public')->delete($photo->path);
            
            // Delete record from database
            $photo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Foto removida com sucesso!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover foto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function historyView(Delivery $delivery)
    {
        try {
            $delivery->load([
                'deliveryRoute',
                'deliveryDriver',
                'deliveryTruck',
                'deliveryCarrocerias',
                'deliveryStops' => function($query) {
                    $query->with(['deliveryRouteStop' => function($q) {
                        $q->with('deliveryRoute');
                    }])->orderBy('order');
                }
            ]);

            return view('deliveries.history', compact('delivery'));
        } catch (Exception $e) {
            \Log::error('Erro ao carregar visualização do histórico', [
                'delivery_id' => $delivery->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Não foi possível carregar o histórico: ' . $e->getMessage());
        }
    }

    public function removeCarroceria(Request $request, Delivery $delivery)
    {
        try {
            $request->validate([
                'carroceria_id' => 'required|exists:carrocerias,id'
            ]);

            $delivery = $this->deliveryService->removeCarroceria(
                $delivery,
                $request->carroceria_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Carroceria removida com sucesso!',
                'data' => new DeliveryResource($delivery)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover carroceria: ' . $e->getMessage()
            ], 422);
        }
    }
}

