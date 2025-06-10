<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryDriver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_id',
        'name',
        'cpf',
        'phone',
        'email',
        'status',
        'cep',
        'state',
        'city',
        'street',
        'number',
        'district'
    ];

    protected $casts = [
        'cnh_expiration' => 'date',
        'status' => 'boolean'
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
} 