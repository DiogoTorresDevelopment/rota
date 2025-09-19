<?php

namespace App\Modules\Passengers\Services;

use App\Models\Passenger;
use App\Repositories\Contracts\PassengerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PassengerService
{
    public function __construct(
        private PassengerRepositoryInterface $passengerRepository
    ) {}

    public function getAllPassengers(): Collection
    {
        return $this->passengerRepository->all();
    }

    public function getPassengerById(int $id): ?Passenger
    {
        return $this->passengerRepository->find($id);
    }

    public function createPassenger(array $data): Passenger
    {
        $data = $this->preparePassengerData($data);
        return $this->passengerRepository->create($data);
    }

    public function updatePassenger(Passenger $passenger, array $data): Passenger
    {
        $data = $this->preparePassengerData($data, true);
        return $this->passengerRepository->update($passenger, $data);
    }

    public function deletePassenger(Passenger $passenger): bool
    {
        return $this->passengerRepository->delete($passenger);
    }

    public function getActivePassengers(): Collection
    {
        return $this->passengerRepository->getActivePassengers();
    }

    public function findPassengerByEmail(string $email): ?Passenger
    {
        return $this->passengerRepository->findByEmail($email);
    }

    public function findPassengerByCpf(string $cpf): ?Passenger
    {
        return $this->passengerRepository->findByCpf($cpf);
    }

    private function preparePassengerData(array $data, bool $isUpdate = false): array
    {
        // Clean CPF
        if (isset($data['cpf'])) {
            $data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);
        }

        // Clean phone
        if (isset($data['phone'])) {
            $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
        }

        // Set default status if not provided
        if (!isset($data['status']) && !$isUpdate) {
            $data['status'] = 'active';
        }

        return $data;
    }
}