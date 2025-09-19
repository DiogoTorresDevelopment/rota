<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\Passengers\Services\PassengerService;
use App\Repositories\Contracts\PassengerRepositoryInterface;
use App\Models\Passenger;
use Mockery;

class PassengerServiceTest extends TestCase
{
    protected $passengerRepository;
    protected $passengerService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->passengerRepository = Mockery::mock(PassengerRepositoryInterface::class);
        $this->passengerService = new PassengerService($this->passengerRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_get_all_passengers()
    {
        $passengers = new \Illuminate\Database\Eloquent\Collection([
            new Passenger(['id' => 1, 'name' => 'John Doe']),
            new Passenger(['id' => 2, 'name' => 'Jane Doe'])
        ]);

        $this->passengerRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($passengers);

        $result = $this->passengerService->getAllPassengers();

        $this->assertCount(2, $result);
        $this->assertEquals('John Doe', $result->first()->name);
    }

    public function test_can_create_passenger_with_prepared_data()
    {
        $inputData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf' => '123.456.789-10',
            'phone' => '(11) 99999-9999'
        ];

        $passenger = new Passenger($inputData);

        $this->passengerRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'John Doe' 
                    && $data['email'] === 'john@example.com'
                    && $data['cpf'] === '12345678910' // Should be cleaned
                    && $data['phone'] === '11999999999' // Should be cleaned
                    && $data['status'] === 'active';
            }))
            ->andReturn($passenger);

        $result = $this->passengerService->createPassenger($inputData);

        $this->assertEquals('John Doe', $result->name);
    }

    public function test_can_find_passenger_by_id()
    {
        $passenger = new Passenger(['id' => 1, 'name' => 'John Doe']);

        $this->passengerRepository
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($passenger);

        $result = $this->passengerService->getPassengerById(1);

        $this->assertEquals('John Doe', $result->name);
    }

    public function test_can_update_passenger()
    {
        $passenger = new Passenger(['id' => 1, 'name' => 'John Doe']);
        $updateData = ['name' => 'Jane Doe'];
        $updatedPassenger = new Passenger(['id' => 1, 'name' => 'Jane Doe']);

        $this->passengerRepository
            ->shouldReceive('update')
            ->with($passenger, Mockery::on(function ($data) {
                return $data['name'] === 'Jane Doe';
            }))
            ->once()
            ->andReturn($updatedPassenger);

        $result = $this->passengerService->updatePassenger($passenger, $updateData);

        $this->assertEquals('Jane Doe', $result->name);
    }

    public function test_can_delete_passenger()
    {
        $passenger = new Passenger(['id' => 1, 'name' => 'John Doe']);

        $this->passengerRepository
            ->shouldReceive('delete')
            ->with($passenger)
            ->once()
            ->andReturn(true);

        $result = $this->passengerService->deletePassenger($passenger);

        $this->assertTrue($result);
    }

    public function test_cleans_cpf_and_phone_data()
    {
        $inputData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf' => '123.456.789-10',
            'phone' => '(11) 99999-9999'
        ];

        $passenger = new Passenger($inputData);

        $this->passengerRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['cpf'] === '12345678910' 
                    && $data['phone'] === '11999999999';
            }))
            ->andReturn($passenger);

        $result = $this->passengerService->createPassenger($inputData);
        
        $this->assertInstanceOf(Passenger::class, $result);
    }

    public function test_sets_default_status_for_new_passenger()
    {
        $inputData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf' => '12345678910'
        ];

        $passenger = new Passenger($inputData);

        $this->passengerRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['status'] === 'active';
            }))
            ->andReturn($passenger);

        $result = $this->passengerService->createPassenger($inputData);
        
        $this->assertInstanceOf(Passenger::class, $result);
    }
}
