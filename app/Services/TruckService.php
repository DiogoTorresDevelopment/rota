<?php

namespace App\Services;

use App\Models\Truck;

class TruckService
{
    public function store(array $data)
    {
        return Truck::create($data);
    }

    public function update(Truck $truck, array $data)
    {
        return $truck->update($data);
    }
} 