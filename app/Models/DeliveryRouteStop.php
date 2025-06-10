<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryRouteStop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_route_id',
        'name',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'cep',
        'latitude',
        'longitude',
        'order'
    ];

    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class);
    }
} 