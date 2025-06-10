<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Carroceria;

class Delivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'route_id',
        'driver_id',
        'truck_id',
        'trailer_id',
        'carroceria_id',
        'status',
        'start_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function trailer()
    {
        return $this->belongsTo(Trailer::class);
    }

    public function carroceria()
    {
        return $this->belongsTo(Carroceria::class);
    }
}
