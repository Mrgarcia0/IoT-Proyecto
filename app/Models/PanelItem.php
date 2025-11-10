<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanelItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'metric',
        'variable_name',
        'variables',
        'window_days',
        'title',
        'position',
        'critical_min',
        'critical_max',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}