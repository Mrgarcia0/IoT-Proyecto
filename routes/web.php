<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReadingController;

Route::get('/', [DashboardController::class, 'index']);

// Detalles de un dispositivo
Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');

// Toggle ON/OFF y redirección a detalles
Route::post('/devices/{device}/toggle', [DeviceController::class, 'toggle'])->name('devices.toggle');

// API simple para simulación y datos (usada por la UI)
Route::get('/devices/{device}/readings', [ReadingController::class, 'readings'])->name('devices.readings');
Route::post('/devices/{device}/simulate-sample', [ReadingController::class, 'simulateSample'])->name('devices.simulateSample');
