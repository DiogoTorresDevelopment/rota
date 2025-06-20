<?php

namespace App\Http\Controllers;

use App\Http\Requests\RouteRequest;
use App\Models\Route;
use App\Services\RouteService;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    protected $routeService;

    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
    }

    public function index()
    {
        try {
            $routes = Route::with(['stops'])
                ->when(request('search'), function($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('routes.index', compact('routes'));
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar rotas:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('routes.index', [
                'routes' => collect([])
            ])->with('error', 'Erro ao carregar rotas');
        }
    }

    public function create()
    {
        return view('routes.create');
    }

    public function store(Request $request)
    {
        $step = $request->input('step', 1);

        try {
            switch ($step) {
                case 1:
                    // Validação específica para etapa 1
                    $validated = $request->validate([
                        'name' => 'required',
                        'start_date' => 'required|date',
                        'current_mileage' => 'required|numeric',
                    ]);

                    // Se já existe uma rota em progresso, atualiza. Senão, cria nova
                    $route = Route::findOrNew($request->route_id);
                    $route->fill($validated);
                    $route->save();

                    return response()->json([
                        'success' => true,
                        'route_id' => $route->id,
                        'message' => 'Dados salvos com sucesso!'
                    ]);
                    break;

                case 2:
                    $validated = $request->validate([
                        'origin.name' => 'required|string|max:255',
                        'origin.schedule' => 'required',
                        'origin.cep' => 'required|string',
                        'origin.state' => 'required|string',
                        'origin.city' => 'required|string',
                        'origin.street' => 'required|string',
                        'origin.number' => 'required|string',
                        'origin.latitude' => 'nullable|numeric|between:-90,90',
                        'origin.longitude' => 'nullable|numeric|between:-180,180',

                        'destination.name' => 'required|string|max:255',
                        'destination.cep' => 'required|string',
                        'destination.state' => 'required|string',
                        'destination.city' => 'required|string',
                        'destination.street' => 'required|string',
                        'destination.number' => 'required|string',
                        'destination.latitude' => 'nullable|numeric|between:-90,90',
                        'destination.longitude' => 'nullable|numeric|between:-180,180',
                    ]);

                    $route = Route::findOrFail($request->route_id);

                    // Salvar endereço de origem
                    $route->addresses()->updateOrCreate(
                        ['type' => 'origin'],
                        [
                            'name' => $validated['origin']['name'],
                            'schedule' => $validated['origin']['schedule'],
                            'cep' => $validated['origin']['cep'],
                            'state' => $validated['origin']['state'],
                            'city' => $validated['origin']['city'],
                            'street' => $validated['origin']['street'],
                            'number' => $validated['origin']['number'],
                            'latitude' => $validated['origin']['latitude'],
                            'longitude' => $validated['origin']['longitude'],
                        ]
                    );

                    // Salvar endereço de destino
                    $route->addresses()->updateOrCreate(
                        ['type' => 'destination'],
                        [
                            'name' => $validated['destination']['name'],
                            'cep' => $validated['destination']['cep'],
                            'state' => $validated['destination']['state'],
                            'city' => $validated['destination']['city'],
                            'street' => $validated['destination']['street'],
                            'number' => $validated['destination']['number'],
                            'latitude' => $validated['destination']['latitude'],
                            'longitude' => $validated['destination']['longitude'],
                        ]
                    );

                    return response()->json([
                        'success' => true,
                        'route_id' => $route->id,
                        'message' => 'Endereços salvos com sucesso!'
                    ]);
                    break;

                case 3:
                    \Log::info('Received destinations data:', [
                        'request' => $request->all(),
                        'destinations' => $request->input('destinations')
                    ]);

                    $validated = $request->validate([
                        'destinations' => 'required|string',
                    ]);

                    $route = Route::findOrFail($request->route_id);
                    $destinations = json_decode($validated['destinations'], true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON data for destinations');
                    }

                    // Remove destinos antigos
                    $route->stops()->delete();

                    // Adiciona os novos destinos
                    foreach ($destinations as $index => $destination) {
                        $route->stops()->create([
                            'name' => $destination['name'],
                            'order' => $index + 1,
                            'cep' => $destination['cep'],
                            'state' => $destination['state'],
                            'city' => $destination['city'],
                            'street' => $destination['street'],
                            'number' => $destination['number'],
                            'latitude' => $destination['latitude'],
                            'longitude' => $destination['longitude']
                        ]);
                    }

                    // Atualiza o status da rota para active
                    $route->update(['status' => 'active']);

                    return response()->json([
                        'success' => true,
                        'route_id' => $route->id,
                        'message' => 'Rota finalizada com sucesso!'
                    ]);

                default:
                    throw new \Exception('Invalid step');
            }

        } catch (\Exception $e) {
            \Log::error('Error in store method:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar os dados: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit(Route $route, Request $request)
    {
        $step = $request->query('step', 1);
        $route->load(['addresses', 'stops']);

        // Para debug
        \Log::info('Route data:', [
            'addresses' => $route->addresses->toArray(),
            'stops' => $route->stops->toArray()
        ]);

        return view('routes.edit', compact('route', 'step'));
    }

    public function update(RouteRequest $request, Route $route)
    {
        return $this->store($request);
    }

    public function destroy(Route $route)
    {
        try {
            \Log::info('Iniciando exclusão da rota: ' . $route->id);

            $this->routeService->delete($route);

            \Log::info('Rota excluída com sucesso: ' . $route->id);

            return response()->json([
                'success' => true,
                'message' => 'Rota excluída com sucesso'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir rota: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir: ' . $e->getMessage()
            ], 422);
        }
    }

    public function optimize(Route $route)
    {
        try {
            $optimizedRoute = $this->routeService->optimizeRouteStops($route);

            return response()->json([
                'success' => true,
                'message' => 'Rota otimizada com sucesso!',
                'data' => $optimizedRoute
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao otimizar rota:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao otimizar rota: ' . $e->getMessage()
            ], 422);
        }
    }

    public function apiDriverRoutes(Request $request)
    {
        $driver = $request->user();

        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Perfil de motorista não encontrado'
            ], 404);
        }

        $routes = Route::whereHas('deliveries', function($query) use ($driver) {
                $query->where('original_driver_id', $driver->id);
            })
            ->with([
                'stops',
                'deliveries' => function($query) {
                    $query->where('status', 'in_progress');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($route) {
                return [
                    'id' => $route->id,
                    'name' => $route->name,
                    'status' => $route->status,
                    'start_date' => $route->start_date,
                    'current_mileage' => $route->current_mileage,
                    'stops' => $route->stops->map(function ($stop) {
                        return [
                            'id' => $stop->id,
                            'name' => $stop->name,
                            'order' => $stop->order,
                            'latitude' => $stop->latitude,
                            'longitude' => $stop->longitude,
                            'address' => [
                                'street' => $stop->street,
                                'number' => $stop->number,
                                'city' => $stop->city,
                                'state' => $stop->state,
                                'cep' => $stop->cep,
                            ]
                        ];
                    }),
                    'deliveries' => $route->deliveries->map(function ($delivery) {
                        return [
                            'id' => $delivery->id,
                            'status' => $delivery->status,
                            'created_at' => $delivery->created_at,
                            'completed_at' => $delivery->end_date,
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
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'routes' => $routes
            ]
        ]);
    }

    public function apiDriverRouteDetails(Request $request, Route $route)
    {
        $driver = $request->user();

        if (!$driver || !$route->deliveries()->where('original_driver_id', $driver->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Rota não encontrada'
            ], 404);
        }

        $route->load([
            'stops',
            'deliveries' => function($query) {
                $query->where('status', 'in_progress')
                    ->with([
                        'currentStop.deliveryRouteStop',
                        'deliveryRoute',
                        'deliveryDriver',
                        'deliveryTruck.carrocerias'
                    ]);
            }
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'route' => [
                    'id' => $route->id,
                    'name' => $route->name,
                    'status' => $route->status,
                    'start_date' => $route->start_date,
                    'current_mileage' => $route->current_mileage,
                    'stops' => $route->stops->map(function ($stop) {
                        return [
                            'id' => $stop->id,
                            'name' => $stop->name,
                            'order' => $stop->order,
                            'latitude' => $stop->latitude,
                            'longitude' => $stop->longitude,
                            'address' => [
                                'street' => $stop->street,
                                'number' => $stop->number,
                                'city' => $stop->city,
                                'state' => $stop->state,
                                'cep' => $stop->cep,
                            ]
                        ];
                    }),
                    'deliveries' => $route->deliveries->map(function ($delivery) {
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
                                'carrocerias' => $delivery->deliveryTruck->carrocerias->map(function($carroceria) {
                                    return [
                                        'id' => $carroceria->id,
                                        'name' => $carroceria->name,
                                        'type' => $carroceria->type
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
                    })
                ]
            ]
        ]);
    }

    public function show(Route $route)
    {
        $route->load(['addresses', 'stops', 'deliveries']);
        return view('routes.show', compact('route'));
    }
}
