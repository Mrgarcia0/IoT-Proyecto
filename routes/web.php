<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReadingController;
use App\Http\Controllers\PanelController;

Route::get('/', [DashboardController::class, 'index']);

// Detalles de un dispositivo
Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('devices.show');
Route::get('/devices/{device}/explorar', [DeviceController::class, 'explorer'])->name('devices.explorer');
Route::get('/devices/{device}/paneles', [PanelController::class, 'index'])->name('devices.panels');
// CRUD de paneles por dispositivo
Route::post('/devices/{device}/paneles', [PanelController::class, 'store'])->name('devices.panels.store');
Route::put('/devices/{device}/paneles/{panelItem}', [PanelController::class, 'update'])->name('devices.panels.update');
Route::delete('/devices/{device}/paneles/{panelItem}', [PanelController::class, 'destroy'])->name('devices.panels.destroy');
Route::get('/devices/{device}/casa', [DeviceController::class, 'home'])->name('devices.home');

// Toggle ON/OFF y redirección a detalles
Route::post('/devices/{device}/toggle', [DeviceController::class, 'toggle'])->name('devices.toggle');

// API simple para simulación y datos (usada por la UI)
Route::get('/devices/{device}/readings', [ReadingController::class, 'readings'])->name('devices.readings');
Route::post('/devices/{device}/simulate-sample', [ReadingController::class, 'simulateSample'])->name('devices.simulateSample');
