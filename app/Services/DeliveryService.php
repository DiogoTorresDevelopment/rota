<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Route;
use App\Models\DeliveryStop;
use App\Models\DeliveryHistory;
use Illuminate\Support\Facades\DB;
use Exception;

class DeliveryService
{
    public function getDeliveries()
    {
        return Delivery::with(['route.driver'])
            ->whereHas('route', function($query) {
                $query->whereNull('deleted_at');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(50);
    }

    public function getAvailableRoutes()
    {
        // Busca rotas que estão ativas e não têm entregas em andamento
        return Route::query()
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->whereDoesntHave('deliveries', function($query) {
                $query->where('status', 'in_progress');
            })
            ->get();
    }

    public function startDelivery($routeId, $driverId, $truckId, array $carroceriaIds)
    {
        try {
            DB::beginTransaction();

            $route = Route::findOrFail($routeId);
            
            // Log para debug
            \Log::info('Verificando entregas ativas', [
                'route_id' => $routeId,
                'active_deliveries' => $route->deliveries()
                    ->where('status', 'in_progress')
                    ->get()
            ]);

            $hasActiveDelivery = $route->deliveries()
                ->where('status', 'in_progress')
                ->exists();

            if ($hasActiveDelivery) {
                throw new Exception('Esta rota já está em andamento em outra entrega');
            }

            $delivery = Delivery::create([
                'route_id' => $route->id,
                'driver_id' => $driverId,
                'truck_id' => $truckId,
                'carroceria_id' => $carroceriaId,
                'status' => 'in_progress',
                'start_date' => now()
            ]);

            // cria paradas
            $firstStopId = null;
            foreach ($route->stops()->orderBy('order')->get() as $stop) {
                $deliveryStop = DeliveryStop::create([
                    'delivery_id' => $delivery->id,
                    'route_stop_id' => $stop->id,
                    'order' => $stop->order,
                ]);
                if (!$firstStopId) {
                    $firstStopId = $deliveryStop->id;
                }
            }
            $delivery->update(['current_delivery_stop_id' => $firstStopId]);

            // vincula carrocerias
            $delivery->carrocerias()->sync($carroceriaIds);

            DeliveryHistory::create([
                'delivery_id' => $delivery->id,
                'driver_id' => $driverId,
                'truck_id' => $truckId,
                'carroceria_ids' => $carroceriaIds,
            ]);

            // Log do sucesso
            \Log::info('Nova entrega criada', ['delivery' => $delivery]);

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

    public function changeResources(Delivery $delivery, $driverId, $truckId, array $carroceriaIds)
    {
        return DB::transaction(function () use ($delivery, $driverId, $truckId, $carroceriaIds) {
            $delivery->update([
                'driver_id' => $driverId,
                'truck_id' => $truckId,
            ]);
            $delivery->carrocerias()->sync($carroceriaIds);

            DeliveryHistory::create([
                'delivery_id' => $delivery->id,
                'delivery_stop_id' => $delivery->current_delivery_stop_id,
                'driver_id' => $driverId,
                'truck_id' => $truckId,
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
        return $delivery->histories()->with('deliveryStop.routeStop')->orderBy('created_at')->get();
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