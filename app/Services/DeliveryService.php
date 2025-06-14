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

    public function getAvailableDrivers()
    {
        // Busca motoristas que não estão em entregas em andamento
        return Driver::where('status', true)
            ->whereDoesntHave('deliveries', function($query) {
                $query->where('status', 'in_progress');
            })
            ->get();
    }

    public function getAvailableTrucks()
    {
        // Busca caminhões que não estão em entregas em andamento
        return Truck::where('status', true)
            ->whereDoesntHave('deliveries', function($query) {
                $query->where('status', 'in_progress');
            })
            ->get();
    }

    public function getAvailableCarrocerias()
    {
        // Busca carrocerias que não estão em entregas em andamento
        return Carroceria::whereNull('deleted_at')
            ->whereDoesntHave('deliveries', function($query) {
                $query->where('status', 'in_progress');
            })
            ->get();
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
            $carroceriasInUse = Delivery::whereHas('deliveryCarrocerias', function($query) use ($carroceriaIds) {
                $query->whereIn('carroceria_id', $carroceriaIds);
            })
            ->where('status', 'in_progress')
            ->exists();
            
            if ($carroceriasInUse) {
                throw new Exception('Uma ou mais carrocerias selecionadas já estão em uma entrega em andamento');
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

            // Cria os snapshots das carrocerias
            foreach ($carrocerias as $carroceria) {
                DeliveryCarroceria::create([
                    'delivery_id' => $delivery->id,
                    'descricao' => $carroceria->descricao,
                    'chassi' => $carroceria->chassi,
                    'placa' => $carroceria->placa,
                    'peso_suportado' => $carroceria->peso_suportado
                ]);
            }

            // Registra no histórico
            DeliveryHistory::create([
                'delivery_id' => $delivery->id,
                'driver_id' => $driver->id,
                'truck_id' => $truck->id,
                'carroceria_ids' => $carroceriaIds,
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

    public function changeResources(Delivery $delivery, $routeId, $driverId, $truckId, array $carroceriaIds, $startDate = null, $endDate = null)
    {
        return DB::transaction(function () use ($delivery, $routeId, $driverId, $truckId, $carroceriaIds, $startDate, $endDate) {
            $route = Route::with('stops')->findOrFail($routeId);
            $driver = Driver::findOrFail($driverId);
            $truck = Truck::findOrFail($truckId);
            $carrocerias = Carroceria::whereIn('id', $carroceriaIds)->get();

            // Verifica se o motorista está disponível
            $driverInUse = Delivery::where('original_driver_id', $driverId)
                ->where('status', 'in_progress')
                ->where('id', '!=', $delivery->id)
                ->exists();
            
            if ($driverInUse) {
                throw new Exception('Este motorista já está em uma entrega em andamento');
            }

            // Verifica se o caminhão está disponível
            $truckInUse = Delivery::where('original_truck_id', $truckId)
                ->where('status', 'in_progress')
                ->where('id', '!=', $delivery->id)
                ->exists();
            
            if ($truckInUse) {
                throw new Exception('Este caminhão já está em uma entrega em andamento');
            }

            // Verifica se alguma das carrocerias está em uso
            $carroceriasInUse = Delivery::whereHas('deliveryCarrocerias', function($query) use ($carroceriaIds) {
                $query->whereIn('carroceria_id', $carroceriaIds);
            })
            ->where('status', 'in_progress')
            ->where('id', '!=', $delivery->id)
            ->exists();
            
            if ($carroceriasInUse) {
                throw new Exception('Uma ou mais carrocerias selecionadas já estão em uma entrega em andamento');
            }

            // Atualiza os dados da entrega
            $delivery->update([
                'original_route_id' => $route->id,
                'original_driver_id' => $driver->id,
                'original_truck_id' => $truck->id,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            // Atualiza o snapshot da rota
            $delivery->deliveryRoute->update([
                'name' => $route->name,
                'description' => $route->description,
                'status' => $route->status
            ]);

            // Atualiza os snapshots das paradas
            $delivery->deliveryRoute->stops()->delete();
            foreach ($route->stops()->orderBy('order')->get() as $stop) {
                DeliveryRouteStop::create([
                    'delivery_route_id' => $delivery->deliveryRoute->id,
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

            // Atualiza os snapshots das carrocerias
            $delivery->deliveryCarrocerias()->delete();
            foreach ($carrocerias as $carroceria) {
                DeliveryCarroceria::create([
                    'delivery_id' => $delivery->id,
                    'descricao' => $carroceria->descricao,
                    'chassi' => $carroceria->chassi,
                    'placa' => $carroceria->placa,
                    'peso_suportado' => $carroceria->peso_suportado
                ]);
            }

            // Registra no histórico
            DeliveryHistory::create([
                'delivery_id' => $delivery->id,
                'delivery_stop_id' => $delivery->current_delivery_stop_id,
                'driver_id' => $driver->id,
                'truck_id' => $truck->id,
                'carroceria_ids' => $carroceriaIds,
            ]);

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
            ->with(['deliveryStop.deliveryRouteStop', 'driver', 'truck'])
            ->orderBy('created_at')
            ->get();
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
} 