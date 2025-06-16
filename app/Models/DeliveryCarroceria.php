<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryCarroceria extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_id',
        'carroceria_id',
        'descricao',
        'chassi',
        'placa',
        'peso_suportado'
    ];

    protected $casts = [
        'peso_suportado' => 'decimal:2'
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function carroceria()
    {
        return $this->belongsTo(Carroceria::class);
    }
} 