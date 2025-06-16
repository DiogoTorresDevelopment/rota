<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Route;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\Carroceria;
use App\Models\DeliveryStop;
use App\Models\DeliveryHistory;
use App\Models\DeliveryRoute;
use App\Models\DeliveryRouteStop;
use App\Models\DeliveryDriver;
use App\Models\DeliveryTruck;
use App\Models\DeliveryCarroceria;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Storage;

class DeliveryService
{
    public function getDeliveries()
    {
        return Delivery::with([
            'deliveryRoute',
            'deliveryDriver',
            'deliveryTruck',
            'deliveryCarrocerias',  
            'deliveryStops.deliveryRouteStop'
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
    }

    public function getAvailableRoutes()
    {
        // Busca rotas que estão ativas
        return Route::query()
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->get();
    }

    public function getAvailableDrivers(Delivery $currentDelivery = null)
    {
        // Busca motoristas que não estão em entregas em andamento
        $query = Driver::where('status', true)
            ->whereDoesntHave('deliveries', function($query) use ($currentDelivery) {
                $query->where('status', 'in_progress')
                    ->when($currentDelivery, function($q) use ($currentDelivery) {
                        $q->where('id', '!=', $currentDelivery->id);
                    });
            });

        // Se for uma edição, inclui o motorista atual
        if ($currentDelivery) {
            $query->orWhere('id', $currentDelivery->original_driver_id);
        }

        return $query->get();
    }

    public function getAvailableTrucks(Delivery $currentDelivery = null)
    {
        // Busca caminhões que não estão em entregas em andamento
        $query = Truck::where('status', true)
            ->whereDoesntHave('deliveries', function($query) use ($currentDelivery) {
                $query->where('status', 'in_progress')
                    ->when($currentDelivery, function($q) use ($currentDelivery) {
                        $q->where('id', '!=', $currentDelivery->id);
                    });
            });

        // Se for uma edição, inclui o caminhão atual
        if ($currentDelivery) {
            $query->orWhere('id', $currentDelivery->original_truck_id);
        }

        return $query->get();
    }

    public function getAvailableCarrocerias(Delivery $currentDelivery = null)
    {
        // Busca carrocerias que não estão em entregas em andamento
        $query = Carroceria::whereNull('carrocerias.deleted_at')
            ->whereDoesntHave('deliveries', function($query) use ($currentDelivery) {
                $query->where('deliveries.status', 'in_progress')
                    ->when($currentDelivery, function($q) use ($currentDelivery) {
                        $q->where('deliveries.id', '!=', $currentDelivery->id);
                    });
            });

        // Se for uma edição, inclui as carrocerias atuais
        if ($currentDelivery) {
            $currentCarroceriaIds = $currentDelivery->deliveryCarrocerias->pluck('carroceria_id')->toArray();
            if (!empty($currentCarroceriaIds)) {
                $query->orWhereIn('carrocerias.id', $currentCarroceriaIds);
            }
        }

        return $query->get();
    }

    public function startDelivery($routeId, $driverId, $truckId, array $carroceriaIds, $startDate = null, $endDate = null)
    {
        try {
            DB::beginTransaction();

            $route = Route::with('stops')->findOrFail($routeId);
            $driver = Driver::findOrFail($driverId);
            $truck = Truck::findOrFail($truckId);
            $carrocerias = Carroceria::whereIn('id', $carroceriaIds)->get();
            
            // Verifica se o motorista está disponível
            $driverInUse = Delivery::where('original_driver_id', $driverId)
                    ->where('status', 'in_progress')
                ->exists();
            
            if ($driverInUse) {
                throw new Exception('Este motorista já está em uma entrega em andamento');
            }

            // Verifica se o caminhão está disponível
            $truckInUse = Delivery::where('original_truck_id', $truckId)
                ->where('status', 'in_progress')
                ->exists();

            if ($truckInUse) {
                throw new Exception('Este caminhão já está em uma entrega em andamento');
            }

            // Verifica se alguma das carrocerias está em uso
            foreach ($carrocerias as $carroceria) {
                if (!$carroceria->isAvailable()) {
                    throw new Exception("A carroceria {$carroceria->descricao} (Placa: {$carroceria->placa}) já está em uma entrega em andamento");
                }
            }

            // Cria a entrega
            $delivery = Delivery::create([
                'original_route_id' => $route->id,
                'original_driver_id' => $driver->id,
                'original_truck_id' => $truck->id,
                'status' => 'in_progress',
                'start_date' => $startDate ?? now(),
                'end_date' => $endDate
            ]);

            // Vincula as carrocerias à entrega
            foreach ($carrocerias as $carroceria) {
                DeliveryCarroceria::create([
                    'delivery_id' => $delivery->id,
                    'carroceria_id' => $carroceria->id,
                    'descricao' => $carroceria->descricao,
                    'chassi' => $carroceria->chassi,
                    'placa' => $carroceria->placa,
                    'peso_suportado' => $carroceria->peso_suportado
                ]);
            }

            // Cria o snapshot da rota
            $deliveryRoute = DeliveryRoute::create([
                'delivery_id' => $delivery->id,
                'name' => $route->name,
                'description' => $route->description,
                'status' => $route->status
            ]);

            // Cria os snapshots das paradas
            $deliveryRouteStops = [];
            foreach ($route->stops()->orderBy('order')->get() as $stop) {
                $deliveryRouteStops[] = DeliveryRouteStop::create([
                    'delivery_route_id' => $deliveryRoute->id,
                    'name' => $stop->name,
                    'street' => $stop->street,
                    'number' => $stop->number,
                    'complement' => $stop->complement,
                    'neighborhood' => $stop->neighborhood,
                    'city' => $stop->city,
                    'state' => $stop->state,
                    'cep' => $stop->cep,
                    'latitude' => $stop->latitude,
                    'longitude' => $stop->longitude,
                    'order' => $stop->order
                ]);
            }

            // Cria as paradas da entrega
            foreach ($deliveryRouteStops as $index => $routeStop) {
                $deliveryStop = DeliveryStop::create([
                    'delivery_id' => $delivery->id,
                    'delivery_route_stop_id' => $routeStop->id,
                    'order' => $routeStop->order,
                ]);

                if ($index === 0) {
                    $delivery->update(['current_delivery_stop_id' => $deliveryStop->id]);
                }
            }

            // Cria o snapshot do motorista
            DeliveryDriver::create([
                'delivery_id' => $delivery->id,
                'name' => $driver->name,
                'cpf' => $driver->cpf,
                'phone' => $driver->phone,
                'email' => $driver->email,
                'status' => $driver->status,
                'cep' => $driver->cep,
                'state' => $driver->state,
                'city' => $driver->city,
                'street' => $driver->street,
                'number' => $driver->number,
                'district' => $driver->district
            ]);

            // Cria o snapshot do caminhão
            DeliveryTruck::create([
                'delivery_id' => $delivery->id,
                'marca' => $truck->marca,
                'modelo' => $truck->modelo,
                'ano' => $truck->ano,
                'cor' => $truck->cor,
                'tipo_combustivel' => $truck->tipo_combustivel,
                'carga_suportada' => $truck->carga_suportada,
                'chassi' => $truck->chassi,
                'placa' => $truck->placa,
                'quilometragem' => $truck->quilometragem,
                'ultima_revisao' => $truck->ultima_revisao,
                'status' => $truck->status
            ]);

            // Registra no histórico com os dados originais
            DeliveryHistory::create([
                'delivery_id' => $delivery->id,
                'driver_id' => $driver->id,
                'truck_id' => $truck->id,
                'carroceria_ids' => $carroceriaIds,
                'is_initial' => true
            ]);

            DB::commit();
            return $delivery;

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Erro ao iniciar entrega', [
                'route_id' => $routeId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function changeResources(Delivery $delivery, $routeId = null, $driverId = null, $truckId = null, array $carroceriaIds = null, $startDate = null, $endDate = null)
    {
        return DB::transaction(function () use ($delivery, $routeId, $driverId, $truckId, $carroceriaIds, $startDate, $endDate) {
            // Não permite alterar a rota
            if ($routeId && $routeId != $delivery->original_route_id) {
                throw new Exception('Não é possível alterar a rota de uma entrega em andamento');
            }

            $updateData = [];
            $hasChanges = false;
            $historyData = [];

            if ($driverId) {
                $driver = Driver::findOrFail($driverId);
                // Verifica se o motorista está disponível
                $driverInUse = Delivery::where('original_driver_id', $driverId)
                    ->where('status', 'in_progress')
                    ->where('id', '!=', $delivery->id)
                    ->exists();
                
                if ($driverInUse) {
                    throw new Exception('Este motorista já está em uma entrega em andamento');
                }

                $updateData['original_driver_id'] = $driver->id;
                $hasChanges = true;
                $historyData['driver_id'] = $driver->id;

                // Atualiza o snapshot do motorista
                $delivery->deliveryDriver->update([
                    'name' => $driver->name,
                    'cpf' => $driver->cpf,
                    'phone' => $driver->phone,
                    'email' => $driver->email,
                    'status' => $driver->status,
                    'cep' => $driver->cep,
                    'state' => $driver->state,
                    'city' => $driver->city,
                    'street' => $driver->street,
                    'number' => $driver->number,
                    'district' => $driver->district
                ]);
            }

            if ($truckId) {
                $truck = Truck::findOrFail($truckId);
                // Verifica se o caminhão está disponível
                $truckInUse = Delivery::where('original_truck_id', $truckId)
                    ->where('status', 'in_progress')
                    ->where('id', '!=', $delivery->id)
                    ->exists();
                
                if ($truckInUse) {
                    throw new Exception('Este caminhão já está em uma entrega em andamento');
                }

                $updateData['original_truck_id'] = $truck->id;
                $hasChanges = true;
                $historyData['truck_id'] = $truck->id;

                // Atualiza o snapshot do caminhão
                $delivery->deliveryTruck->update([
                    'marca' => $truck->marca,
                    'modelo' => $truck->modelo,
                    'placa' => $truck->placa,
                    'chassi' => $truck->chassi,
                    'ano' => $truck->ano,
                    'ano_modelo' => $truck->ano_modelo,
                    'cor' => $truck->cor,
                    'status' => $truck->status,
                    'ultima_revisao' => $truck->ultima_revisao
                ]);
            }

            if ($carroceriaIds) {
                $carrocerias = Carroceria::whereIn('id', $carroceriaIds)->get();
                
                // Verifica se alguma das carrocerias está em uso
                foreach ($carrocerias as $carroceria) {
                    if (!$carroceria->isAvailable() && !in_array($carroceria->id, $delivery->carrocerias->pluck('id')->toArray())) {
                        throw new Exception("A carroceria {$carroceria->descricao} (Placa: {$carroceria->placa}) já está em uma entrega em andamento");
                    }
                }

                $hasChanges = true;
                $historyData['carroceria_ids'] = $carroceriaIds;

                // Remove os registros antigos
                $delivery->deliveryCarrocerias()->delete();

                // Cria os novos registros com os dados atualizados
                foreach ($carrocerias as $carroceria) {
                    DeliveryCarroceria::create([
                        'delivery_id' => $delivery->id,
                        'carroceria_id' => $carroceria->id,
                        'descricao' => $carroceria->descricao,
                        'chassi' => $carroceria->chassi,
                        'placa' => $carroceria->placa,
                        'peso_suportado' => $carroceria->peso_suportado
                    ]);
                }

                // Atualiza o vínculo na tabela pivot
                $delivery->carrocerias()->sync($carroceriaIds);
            }

            if ($startDate) {
                $updateData['start_date'] = $startDate;
                $hasChanges = true;
            }

            if ($endDate) {
                $updateData['end_date'] = $endDate;
                $hasChanges = true;
            }

            // Atualiza os dados da entrega
            if (!empty($updateData)) {
                $delivery->update($updateData);
            }

            // Registra no histórico apenas se houver mudanças
            if ($hasChanges) {
                DeliveryHistory::create(array_merge([
                    'delivery_id' => $delivery->id,
                    'delivery_stop_id' => $delivery->current_delivery_stop_id,
                ], $historyData));
            }

            return $delivery->refresh();
        });
    }

    public function completeCurrentStop(Delivery $delivery)
    {
        return DB::transaction(function () use ($delivery) {
            $current = $delivery->currentStop;
            if (!$current) {
                return $delivery;
            }

            $current->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $next = $delivery->deliveryStops()->where('order', '>', $current->order)->orderBy('order')->first();
            if ($next) {
                $delivery->update(['current_delivery_stop_id' => $next->id]);
            } else {
                $this->completeDelivery($delivery);
            }

            return $delivery->refresh();
        });
    }

    public function completeDelivery(Delivery $delivery)
    {
        try {
            DB::beginTransaction();

            if ($delivery->status !== 'in_progress') {
                throw new Exception('Esta entrega não está em andamento');
            }

            // Marca todas as paradas como concluídas
            $delivery->deliveryStops()->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            $delivery->update([
                'status' => 'completed',
                'end_date' => now()
            ]);

            DB::commit();
            return $delivery;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function cancelDelivery(Delivery $delivery)
    {
        return DB::transaction(function () use ($delivery) {
            $delivery->update([
                'status' => 'cancelled',
                'end_date' => now(),
            ]);
            return $delivery;
        });
    }

    public function getHistory(Delivery $delivery)
    {
        return $delivery->histories()
            ->with([
                'deliveryStop' => function($query) {
                    $query->with(['deliveryRouteStop' => function($q) {
                        $q->with('deliveryRoute');
                    }]);
                },
                'driver',
                'truck',
                'carrocerias'
            ])
            ->orderBy('created_at')
            ->get()
            ->map(function ($history) {
                if ($history->deliveryStop && $history->deliveryStop->deliveryRouteStop) {
                    $history->stop_info = [
                        'name' => $history->deliveryStop->deliveryRouteStop->name,
                        'order' => $history->deliveryStop->order,
                        'address' => [
                            'street' => $history->deliveryStop->deliveryRouteStop->street,
                            'number' => $history->deliveryStop->deliveryRouteStop->number,
                            'complement' => $history->deliveryStop->deliveryRouteStop->complement,
                            'neighborhood' => $history->deliveryStop->deliveryRouteStop->neighborhood,
                            'city' => $history->deliveryStop->deliveryRouteStop->city,
                            'state' => $history->deliveryStop->deliveryRouteStop->state,
                            'cep' => $history->deliveryStop->deliveryRouteStop->cep
                        ],
                        'route' => [
                            'name' => $history->deliveryStop->deliveryRouteStop->deliveryRoute->name,
                            'description' => $history->deliveryStop->deliveryRouteStop->deliveryRoute->description
                        ]
                    ];
                }
                return $history;
            });
    }

    public function reuseRoute(Delivery $delivery)
    {
        try {
            DB::beginTransaction();

            // Verifica se não existe outra entrega em andamento para esta rota
            $hasActiveDelivery = $delivery->route->deliveries()
                ->where('status', 'in_progress')
                ->exists();

            if ($hasActiveDelivery) {
                throw new Exception('Esta rota já possui uma entrega em andamento');
            }

            // Cria uma nova entrega para a rota
            $newDelivery = Delivery::create([
                'route_id' => $delivery->route_id,
                'status' => 'in_progress',
                'start_date' => now()
            ]);

            DB::commit();
            return $newDelivery;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
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

    public function removeCarroceria(Delivery $delivery, $carroceriaId)
    {
        return DB::transaction(function () use ($delivery, $carroceriaId) {
            // Verifica se a carroceria existe na entrega
            $carroceria = $delivery->deliveryCarrocerias()
                ->where('carroceria_id', $carroceriaId)
                ->first();

            if (!$carroceria) {
                throw new Exception('Carroceria não encontrada nesta entrega');
            }

            // Remove o registro da tabela delivery_carrocerias
            $carroceria->delete();

            // Remove o vínculo na tabela pivot
            $delivery->carrocerias()->detach($carroceriaId);

            // Registra no histórico
            DeliveryHistory::create([
                'delivery_id' => $delivery->id,
                'delivery_stop_id' => $delivery->current_delivery_stop_id,
                'carroceria_ids' => $delivery->carrocerias->pluck('id')->toArray(),
                'description' => "Carroceria {$carroceria->descricao} (Placa: {$carroceria->placa}) removida da entrega"
            ]);

            return $delivery->refresh();
        });
    }
} 