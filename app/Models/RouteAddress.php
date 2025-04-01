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
        'complement'
    ];

    protected $casts = [
        'schedule' => 'datetime:H:i',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
} 