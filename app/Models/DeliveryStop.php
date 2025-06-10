<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryStop extends Model
{
    protected $fillable = [
        'delivery_id',
        'route_stop_id',
        'order',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function routeStop()
    {
        return $this->belongsTo(RouteStop::class);
    }
}
