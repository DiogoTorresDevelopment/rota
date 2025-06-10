<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    protected $fillable = [
        'delivery_id',
        'delivery_stop_id',
        'driver_id',
        'truck_id',
        'carroceria_ids',
    ];

    protected $casts = [
        'carroceria_ids' => 'array',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function deliveryStop()
    {
        return $this->belongsTo(DeliveryStop::class);
    }
}
