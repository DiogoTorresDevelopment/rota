<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trailer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'marca',
        'modelo',
        'ano',
        'cor',
        'carga_suportada',
        'chassi',
        'placa',
        'quilometragem',
        'status'
    ];

    protected $casts = [
        'ano' => 'integer',
        'carga_suportada' => 'decimal:2',
        'quilometragem' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
