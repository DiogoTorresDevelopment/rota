<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Carroceria extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'descricao',
        'chassi',
        'placa',
        'peso_suportado',
        'status'
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
