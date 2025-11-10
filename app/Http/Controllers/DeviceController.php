<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeviceController extends Controller
{
    /**
     * Mostrar detalles de un dispositivo.
     */
    public function show(Device $device): View
    {
        return view('devices.show', [
            'device' => $device,
        ]);
    }

    /**
     * Explorador de datos (gráfica) con barra lateral.
     */
    public function explorer(Device $device): View
    {
        return view('devices.explorer', [
            'device' => $device,
        ]);
    }

    /**
     * Paneles (placeholder) con barra lateral.
     */
    public function panels(Device $device): View
    {
        return view('devices.panels', [
            'device' => $device,
        ]);
    }

    /**
     * Casa (placeholder) con barra lateral.
     */
    public function home(Device $device): View
    {
        // Listado rápido de variables para el "lobby"
        $variables = \App\Models\SensorReading::where('device_id', $device->id)
            ->select('variable_name')
            ->distinct()
            ->pluck('variable_name');

        return view('devices.home', [
            'device' => $device,
            'variables' => $variables,
        ]);
    }

    /**
     * Alternar el estado is_active y redirigir a detalles.
     */
    public function toggle(Request $request, Device $device)
    {
        $device->is_active = !$device->is_active;
        $device->save();

        if ($request->expectsJson()) {
            return response()->json([
                'is_active' => $device->is_active,
            ]);
        }

        return redirect()->route('devices.show', $device);
    }

    /**
     * Actualiza ajustes del dispositivo (Casa): setpoints, brillo, gas, etc.
     */
    public function updateSettings(Request $request, Device $device)
    {
        $payload = $request->validate([
            'temperature_target' => 'nullable|numeric',
            'light_level' => 'nullable|integer|min:0|max:100',
            'gas_valve_open' => 'nullable|boolean',
            'power_profile' => 'nullable|string|in:eco,normal,high',
        ]);

        $settings = $device->settings ? json_decode($device->settings, true) : [];
        $settings = array_merge($settings, $payload);
        $device->settings = json_encode($settings);
        $device->save();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'settings' => $settings]);
        }

        return redirect()->route('devices.home', $device)->with('status', 'Ajustes actualizados');
    }
}