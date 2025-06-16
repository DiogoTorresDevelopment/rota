<?php

namespace Tests\Feature\Driver;

use App\Models\{Driver,Truck,Carroceria,Route,RouteStop};
use App\Services\DeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class DriverCompleteStopTest extends TestCase
{
    use RefreshDatabase;

    private function createEnv()
    {
        $driver = Driver::create([
            'name' => 'Driver',
            'cpf' => '12345678902',
            'phone' => '999999999',
            'email' => 'driver3@example.com',
            'password' => 'secret',
            'cep' => '00000000',
            'state' => 'SP',
            'city' => 'Sao Paulo',
            'street' => 'Rua Teste',
            'number' => '123',
            'district' => 'Centro',
            'status' => true,
        ]);

        $truck = Truck::create([
            'marca' => 'Ford',
            'modelo' => 'F4000',
            'ano' => 2020,
            'cor' => 'Branco',
            'tipo_combustivel' => 'Diesel',
            'carga_suportada' => 1000,
            'chassi' => 'CHASSI123',
            'placa' => 'AAA1234',
            'quilometragem' => 0,
            'ultima_revisao' => now(),
            'status' => true,
        ]);

        $route = Route::create([
            'name' => 'Rota Teste',
            'start_date' => now(),
            'driver_id' => $driver->id,
            'truck_id' => $truck->id,
            'current_mileage' => 0,
            'status' => 'active',
        ]);

        RouteStop::create([
            'route_id' => $route->id,
            'name' => 'Parada 1',
            'street' => 'Rua 1',
            'number' => '10',
            'complement' => null,
            'neighborhood' => 'Bairro',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'cep' => '00000000',
            'latitude' => 0,
            'longitude' => 0,
            'order' => 1,
        ]);

        RouteStop::create([
            'route_id' => $route->id,
            'name' => 'Parada 2',
            'street' => 'Rua 2',
            'number' => '20',
            'complement' => null,
            'neighborhood' => 'Bairro',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'cep' => '00000000',
            'latitude' => 0,
            'longitude' => 0,
            'order' => 2,
        ]);

        $carroceria = Carroceria::create([
            'descricao' => 'Carroceria',
            'chassi' => 'CAR123',
            'placa' => 'BBB1234',
            'peso_suportado' => 1000,
            'status' => true,
        ]);

        $service = new DeliveryService();
        $delivery = $service->startDelivery($route->id, $driver->id, $truck->id, [$carroceria->id]);

        return [$driver, $delivery];
    }

    public function test_driver_can_complete_stop()
    {
        [$driver, $delivery] = $this->createEnv();
        $token = JWTAuth::fromUser($driver);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/driver/deliveries/' . $delivery->id . '/complete-stop');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
