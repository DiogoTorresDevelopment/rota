<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverDocument extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'driver_id',
        'type',
        'file_path',
        'original_name'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
} 