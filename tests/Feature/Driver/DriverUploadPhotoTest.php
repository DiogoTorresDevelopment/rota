<?php

namespace Tests\Feature\Driver;

use App\Models\{Driver,Truck,Carroceria,Route,RouteStop};
use App\Services\DeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class DriverUploadPhotoTest extends TestCase
{
    use RefreshDatabase;

    private function createEnv()
    {
        $driver = Driver::create([
            'name' => 'Driver',
            'cpf' => '12345678903',
            'phone' => '999999999',
            'email' => 'driver4@example.com',
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
            'chassi' => 'CHASSI124',
            'placa' => 'CCC1234',
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

        $carroceria = Carroceria::create([
            'descricao' => 'Carroceria',
            'chassi' => 'CAR124',
            'placa' => 'DDD1234',
            'peso_suportado' => 1000,
            'status' => true,
        ]);

        $service = new DeliveryService();
        $delivery = $service->startDelivery($route->id, $driver->id, $truck->id, [$carroceria->id]);

        return [$driver, $delivery];
    }

    public function test_driver_can_upload_photo()
    {
        [$driver, $delivery] = $this->createEnv();
        $token = JWTAuth::fromUser($driver);

        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/driver/upload-photo', [
            'photo' => $file,
            'delivery_id' => $delivery->id,
            'stop_id' => $delivery->currentStop->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
