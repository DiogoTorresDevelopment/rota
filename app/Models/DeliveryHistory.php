<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\Carroceria;

class DeliveryHistory extends Model
{
    protected $fillable = [
        'delivery_id',
        'delivery_stop_id',
        'driver_id',
        'truck_id',
        'carroceria_ids',
        'is_initial',
    ];

    protected $casts = [
        'carroceria_ids' => 'array',
        'is_initial' => 'boolean',
    ];

    protected $appends = ['carrocerias'];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function deliveryStop()
    {
        return $this->belongsTo(DeliveryStop::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function getCarroceriasAttribute()
    {
        if (!$this->carroceria_ids) {
            return collect();
        }
        return Carroceria::whereIn('id', $this->carroceria_ids)->get();
    }
}
