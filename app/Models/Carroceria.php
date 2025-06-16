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
        'peso_suportado'
    ];

    public function deliveries()
    {
        return $this->belongsToMany(Delivery::class, 'delivery_carrocerias', 'carroceria_id', 'delivery_id')
            ->withTimestamps();
    }

    public function deliveryCarrocerias()
    {
        return $this->hasMany(DeliveryCarroceria::class);
    }

    public function isAvailable()
    {
        return !$this->deliveries()
            ->where('status', 'in_progress')
            ->exists();
    }
}
