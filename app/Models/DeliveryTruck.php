<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryTruck extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_id',
        'marca',
        'modelo',
        'ano',
        'cor',
        'tipo_combustivel',
        'carga_suportada',	
        'chassi',	
        'placa',
        'quilometragem',
        'ultima_revisao',
        'status',
        
    ];

    protected $casts = [
        'status' => 'boolean',
        'ultima_revisao' => 'date'
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
} 

