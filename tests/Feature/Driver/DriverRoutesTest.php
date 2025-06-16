<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class DriverRoutesTest extends TestCase
{
    use RefreshDatabase;

    private function createDriver()
    {
        return Driver::create([
            'name' => 'Driver',
            'cpf' => '12345678901',
            'phone' => '999999999',
            'email' => 'driver2@example.com',
            'password' => 'secret',
            'cep' => '00000000',
            'state' => 'SP',
            'city' => 'Sao Paulo',
            'street' => 'Rua Teste',
            'number' => '123',
            'district' => 'Centro',
            'status' => true,
        ]);
    }

    public function test_authenticated_driver_can_fetch_routes()
    {
        $driver = $this->createDriver();
        $token = JWTAuth::fromUser($driver);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/driver/routes');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
