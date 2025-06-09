<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Driver extends Authenticatable
{
    use SoftDeletes, HasApiTokens;

    protected $fillable = [
        'name',
        'cpf',
        'phone',
        'email',
        'password',
        'status',
        'cep',
        'state',
        'city',
        'street',
        'number',
        'district'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token'
    ];

    protected $casts = [
        'cnh_expiration' => 'date',
        'status' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
} 
