<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RouteAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'route_id',
        'type',
        'name',
        'schedule',
        'cep',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
        'latitude',
        'longitude',
        'place_id',
        'formatted_address'
    ];

    protected $casts = [
        'schedule' => 'datetime:H:i',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
} 