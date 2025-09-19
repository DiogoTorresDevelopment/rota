<?php

namespace App\Repositories\Contracts;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;

interface DriverRepositoryInterface
{
    public function all(): Collection;
    
    public function find(int $id): ?Driver;
    
    public function create(array $data): Driver;
    
    public function update(Driver $driver, array $data): Driver;
    
    public function delete(Driver $driver): bool;
    
    public function findByEmail(string $email): ?Driver;
    
    public function findWithDocuments(int $id): ?Driver;
    
    public function getActiveDrivers(): Collection;
}