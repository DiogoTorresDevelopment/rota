<?php

namespace App\Repositories\Eloquent;

use App\Models\Passenger;
use App\Repositories\Contracts\PassengerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PassengerRepository implements PassengerRepositoryInterface
{
    public function all(): Collection
    {
        return Passenger::all();
    }
    
    public function find(int $id): ?Passenger
    {
        return Passenger::find($id);
    }
    
    public function create(array $data): Passenger
    {
        return Passenger::create($data);
    }
    
    public function update(Passenger $passenger, array $data): Passenger
    {
        $passenger->update($data);
        return $passenger->fresh();
    }
    
    public function delete(Passenger $passenger): bool
    {
        return $passenger->delete();
    }
    
    public function findByEmail(string $email): ?Passenger
    {
        return Passenger::where('email', $email)->first();
    }
    
    public function findByCpf(string $cpf): ?Passenger
    {
        return Passenger::where('cpf', $cpf)->first();
    }
    
    public function getActivePassengers(): Collection
    {
        return Passenger::where('status', 'active')->get();
    }
}