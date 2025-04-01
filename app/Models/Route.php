<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'driver_id',
        'truck_id',
        'current_mileage',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'current_mileage' => 'decimal:2'
    ];

    // Relacionamentos
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function addresses()
    {
        return $this->hasMany(RouteAddress::class);
    }

    public function stops()
    {
        return $this->hasMany(RouteStop::class)->orderBy('order');
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    // Métodos auxiliares
    public function origin()
    {
        return $this->addresses()->where('type', 'origin')->first();
    }

    public function destination()
    {
        return $this->addresses()->where('type', 'destination')->first();
    }
} 