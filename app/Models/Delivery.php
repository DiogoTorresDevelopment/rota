<?php

namespace App\Models;

use App\Traits\HasDeliveryLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Carroceria;
use App\Models\DeliveryStop;
use App\Models\DeliveryHistory;

class Delivery extends Model
{
    use SoftDeletes, HasDeliveryLogs;

    protected $fillable = [
        'original_route_id',
        'original_driver_id',
        'original_truck_id',
        'current_delivery_stop_id',
        'status',
        'start_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    // Relationships with original data
    public function route()
    {
        return $this->belongsTo(Route::class, 'original_route_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'original_driver_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'original_truck_id');
    }

    // Relationships with snapshot data
    public function deliveryRoute()
    {
        return $this->hasOne(DeliveryRoute::class);
    }

    public function deliveryDriver()
    {
        return $this->hasOne(DeliveryDriver::class);
    }

    public function deliveryTruck()
    {
        return $this->hasOne(DeliveryTruck::class);
    }

    public function deliveryCarrocerias()
    {
        return $this->hasMany(DeliveryCarroceria::class);
    }

    public function deliveryStops()
    {
        return $this->hasMany(DeliveryStop::class);
    }

    public function currentStop()
    {
        return $this->belongsTo(DeliveryStop::class, 'current_delivery_stop_id');
    }

    public function histories()
    {
        return $this->hasMany(DeliveryHistory::class);
    }
}
