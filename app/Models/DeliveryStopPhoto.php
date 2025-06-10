<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class DeliveryStopPhoto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_stop_id',
        'path',
        'original_name',
        'mime_type',
        'size',
        'description'
    ];

    public function deliveryStop()
    {
        return $this->belongsTo(DeliveryStop::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }
} 