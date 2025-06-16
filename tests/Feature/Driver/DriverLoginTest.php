<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_login_and_receive_token()
    {
        $driver = Driver::create([
            'name' => 'Test Driver',
            'cpf' => '12345678900',
            'phone' => '999999999',
            'email' => 'driver@example.com',
            'password' => 'secret',
            'cep' => '00000000',
            'state' => 'SP',
            'city' => 'Sao Paulo',
            'street' => 'Rua Teste',
            'number' => '123',
            'district' => 'Centro',
            'status' => true,
        ]);

        $response = $this->postJson('/api/login/driver', [
            'email' => $driver->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['token']
            ]);
    }
}
