<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
        'status' => 'boolean',
    ];

    public function documents()
    {
        return $this->hasMany(DriverDocument::class);
    }
} 