<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RouteStop extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'route_id',
        'name',
        'order',
        'cep',
        'state',
        'city',
        'district',
        'street',
        'number',
        'complement',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'order' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->street}, {$this->number} - {$this->district}, {$this->city} - {$this->state}, {$this->cep}";
    }
} 