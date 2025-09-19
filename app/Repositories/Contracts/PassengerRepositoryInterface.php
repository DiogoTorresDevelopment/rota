<?php

namespace App\Repositories\Contracts;

use App\Models\Passenger;
use Illuminate\Database\Eloquent\Collection;

interface PassengerRepositoryInterface
{
    public function all(): Collection;
    
    public function find(int $id): ?Passenger;
    
    public function create(array $data): Passenger;
    
    public function update(Passenger $passenger, array $data): Passenger;
    
    public function delete(Passenger $passenger): bool;
    
    public function findByEmail(string $email): ?Passenger;
    
    public function findByCpf(string $cpf): ?Passenger;
    
    public function getActivePassengers(): Collection;
}