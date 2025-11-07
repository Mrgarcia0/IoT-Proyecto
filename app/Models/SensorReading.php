<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    protected $fillable = [
        'device_id', 'variable_name', 'value', 'unit', 'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'value' => 'float',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
