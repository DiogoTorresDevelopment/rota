<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryRoute extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_id',
        'name',
        'description',
        'status'
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function stops()
    {
        return $this->hasMany(DeliveryRouteStop::class);
    }
} 