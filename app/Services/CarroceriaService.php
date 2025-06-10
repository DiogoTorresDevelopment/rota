<?php

namespace App\Services;

use App\Models\Carroceria;

class CarroceriaService
{
    public function store(array $data)
    {
        return Carroceria::create($data);
    }

    public function update(Carroceria $carroceria, array $data)
    {
        return $carroceria->update($data);
    }
}
