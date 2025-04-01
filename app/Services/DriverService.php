<?php

namespace App\Services;

use App\Models\Driver;

class DriverService
{
    public function store(array $data)
    {
        return Driver::create($data);
    }

    // ... outros métodos do service
} 