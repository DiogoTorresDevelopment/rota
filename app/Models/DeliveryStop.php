<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryStop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_id',
        'delivery_route_stop_id',
        'order',
        'status',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime'
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function deliveryRouteStop()
    {
        return $this->belongsTo(DeliveryRouteStop::class);
    }

    public function photos()
    {
        return $this->hasMany(DeliveryStopPhoto::class);
    }
}
