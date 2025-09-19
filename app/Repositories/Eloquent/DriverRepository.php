<?php

namespace App\Repositories\Eloquent;

use App\Models\Driver;
use App\Repositories\Contracts\DriverRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DriverRepository implements DriverRepositoryInterface
{
    public function all(): Collection
    {
        return Driver::all();
    }
    
    public function find(int $id): ?Driver
    {
        return Driver::find($id);
    }
    
    public function create(array $data): Driver
    {
        return Driver::create($data);
    }
    
    public function update(Driver $driver, array $data): Driver
    {
        $driver->update($data);
        return $driver->fresh();
    }
    
    public function delete(Driver $driver): bool
    {
        return $driver->delete();
    }
    
    public function findByEmail(string $email): ?Driver
    {
        return Driver::where('email', $email)->first();
    }
    
    public function findWithDocuments(int $id): ?Driver
    {
        return Driver::with('documents')->find($id);
    }
    
    public function getActiveDrivers(): Collection
    {
        return Driver::where('status', 'active')->get();
    }
}