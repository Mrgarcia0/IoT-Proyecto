<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;

Route::get('/', [DashboardController::class, 'index']);

// Detalles de un dispositivo
Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');

// Toggle ON/OFF y redirección a detalles
Route::post('/devices/{device}/toggle', [DeviceController::class, 'toggle'])->name('devices.toggle');
