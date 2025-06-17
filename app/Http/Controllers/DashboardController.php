<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Truck;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Rotas ativas com suas paradas e entregas
            $activeRoutes = Route::with(['stops', 'driver', 'deliveries' => function($query) {
                $query->where('status', 'in_progress');
            }])
            ->whereHas('deliveries', function($query) {
                $query->where('status', 'in_progress');
            })
            ->get();

            $recentDeliveries = Delivery::with([
                'deliveryRoute',
                'deliveryDriver',
                'deliveryStops' => function($query) {
                    $query->orderBy('order');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($delivery) {
                // Calcula o progresso baseado nas paradas completadas
                $totalStops = $delivery->deliveryStops->count();
                $completedStops = $delivery->deliveryStops->where('status', 'completed')->count();
                
                $delivery->progress = match($delivery->status) {
                    'completed' => 100,
                    'cancelled' => 0,
                    'in_progress' => $totalStops > 0 ? round(($completedStops / $totalStops) * 100) : 0,
                    default => 0
                };
                return $delivery;
            });

            return view('dashboard', [
                'totalRoutes' => Route::count(),
                'activeDeliveries' => Delivery::where('status', 'in_progress')->count(),
                'activeDrivers' => Driver::count(),
                'totalTrucks' => Truck::count(),
                'activeRoutes' => $activeRoutes,
                'recentDeliveries' => $recentDeliveries
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao carregar dashboard:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('dashboard', [
                'totalRoutes' => 0,
                'activeDeliveries' => 0,
                'activeDrivers' => 0,
                'totalTrucks' => 0,
                'activeRoutes' => collect([]),
                'recentDeliveries' => collect([])
            ])->with('error', 'Erro ao carregar dados do dashboard');
        }
    }

    private function calculateDeliveryProgress(Delivery $delivery)
    {
        // Se a entrega estiver finalizada, retorna 100%
        if ($delivery->status === 'completed') {
            return 100;
        }

        // Se a entrega estiver cancelada, retorna 0%
        if ($delivery->status === 'cancelled') {
            return 0;
        }

        // Para entregas em andamento, você pode implementar uma lógica mais específica
        // Por exemplo, baseado no número de paradas completadas
        $totalStops = $delivery->route->stops->count();
        $completedStops = 0; // Aqui você precisaria ter um campo para rastrear paradas completadas

        if ($totalStops === 0) {
            return 0;
        }

        return round(($completedStops / $totalStops) * 100);
    }
} 