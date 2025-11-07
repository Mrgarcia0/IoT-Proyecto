<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'name', 'type', 'location', 'is_active', 'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    // Relación con lecturas
    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    // Nombre mostrado en UI según requerimiento
    public function getDisplayNameAttribute(): string
    {
        switch ($this->id) {
            case 2:
                return 'Sensor de consumo eléctrico';
            case 3:
                return 'Sensor de calidad del aire';
            case 4:
                return 'Sensor de humo y gas';
            case 5:
                return 'Sensor de polvo';
            default:
                return $this->name;
        }
    }
}
