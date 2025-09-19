<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Modules\Drivers\Services\DriverService;
use App\Repositories\Contracts\DriverRepositoryInterface;
use App\Models\Driver;
use Mockery;

class DriverServiceTest extends TestCase
{
    protected $driverRepository;
    protected $driverService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->driverRepository = Mockery::mock(DriverRepositoryInterface::class);
        $this->driverService = new DriverService($this->driverRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_can_get_all_drivers()
    {
        $drivers = new \Illuminate\Database\Eloquent\Collection([
            new Driver(['id' => 1, 'name' => 'John Doe']),
            new Driver(['id' => 2, 'name' => 'Jane Doe'])
        ]);

        $this->driverRepository
            ->shouldReceive('all')
            ->once()
            ->andReturn($drivers);

        $result = $this->driverService->getAllDrivers();

        $this->assertCount(2, $result);
        $this->assertEquals('John Doe', $result->first()->name);
    }

    public function test_can_create_driver_with_prepared_data()
    {
        $inputData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ];

        $driver = new Driver($inputData);

        $this->driverRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return $data['name'] === 'John Doe' 
                    && $data['email'] === 'john@example.com'
                    && $data['status'] === 'active'
                    && isset($data['password']); // Password should be hashed
            }))
            ->andReturn($driver);

        $result = $this->driverService->createDriver($inputData);

        $this->assertEquals('John Doe', $result->name);
    }

    public function test_can_find_driver_by_id()
    {
        $driver = new Driver(['id' => 1, 'name' => 'John Doe']);

        $this->driverRepository
            ->shouldReceive('find')
            ->with(1)
            ->once()
            ->andReturn($driver);

        $result = $this->driverService->getDriverById(1);

        $this->assertEquals('John Doe', $result->name);
    }

    public function test_can_update_driver()
    {
        $driver = new Driver(['id' => 1, 'name' => 'John Doe']);
        $updateData = ['name' => 'Jane Doe'];
        $updatedDriver = new Driver(['id' => 1, 'name' => 'Jane Doe']);

        $this->driverRepository
            ->shouldReceive('update')
            ->with($driver, Mockery::on(function ($data) {
                return $data['name'] === 'Jane Doe';
            }))
            ->once()
            ->andReturn($updatedDriver);

        $result = $this->driverService->updateDriver($driver, $updateData);

        $this->assertEquals('Jane Doe', $result->name);
    }

    public function test_can_delete_driver()
    {
        $driver = new Driver(['id' => 1, 'name' => 'John Doe']);

        $this->driverRepository
            ->shouldReceive('delete')
            ->with($driver)
            ->once()
            ->andReturn(true);

        $result = $this->driverService->deleteDriver($driver);

        $this->assertTrue($result);
    }

    public function test_creates_random_password_when_not_provided()
    {
        $inputData = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $driver = new Driver($inputData);

        $this->driverRepository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return isset($data['password']) && !empty($data['password']);
            }))
            ->andReturn($driver);

        $result = $this->driverService->createDriver($inputData);
        
        $this->assertInstanceOf(Driver::class, $result);
    }
}
