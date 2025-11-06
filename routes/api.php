<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Prefijar los nombres para evitar colisión con rutas web (devices.show)
Route::apiResource('devices', DeviceController::class)->names('api.devices');
Route::get('devices/{device}/sensor-readings', [DeviceController::class, 'sensorReadings']);