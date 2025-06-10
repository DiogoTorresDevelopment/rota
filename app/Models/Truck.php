<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
        'status'
    ];

    protected $casts = [
        'ano' => 'integer',
        'carga_suportada' => 'decimal:2',
        'quilometragem' => 'decimal:2',
        'status' => 'boolean',
        'ultima_revisao' => 'date',
    ];

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'original_truck_id');
    }
}
