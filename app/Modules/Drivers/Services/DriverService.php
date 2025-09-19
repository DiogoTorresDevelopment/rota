<?php

namespace App\Modules\Drivers\Services;

use App\Models\Driver;
use App\Repositories\Contracts\DriverRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;

class DriverService
{
    public function __construct(
        private DriverRepositoryInterface $driverRepository
    ) {}

    public function getAllDrivers(): Collection
    {
        return $this->driverRepository->all();
    }

    public function getDriverById(int $id): ?Driver
    {
        return $this->driverRepository->find($id);
    }

    public function createDriver(array $data): Driver
    {
        $data = $this->prepareDriverData($data);
        return $this->driverRepository->create($data);
    }

    public function updateDriver(Driver $driver, array $data): Driver
    {
        $data = $this->prepareDriverData($data, true);
        return $this->driverRepository->update($driver, $data);
    }

    public function deleteDriver(Driver $driver): bool
    {
        return $this->driverRepository->delete($driver);
    }

    public function getDriverWithDocuments(int $id): ?Driver
    {
        return $this->driverRepository->findWithDocuments($id);
    }

    public function getActiveDrivers(): Collection
    {
        return $this->driverRepository->getActiveDrivers();
    }

    public function findDriverByEmail(string $email): ?Driver
    {
        return $this->driverRepository->findByEmail($email);
    }

    private function prepareDriverData(array $data, bool $isUpdate = false): array
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } elseif (!$isUpdate) {
            // Generate random password for new drivers if not provided
            $data['password'] = Hash::make(Str::random(8));
        }

        // Set default status if not provided
        if (!isset($data['status']) && !$isUpdate) {
            $data['status'] = 'active';
        }

        return $data;
    }
}