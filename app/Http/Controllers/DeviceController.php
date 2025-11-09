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
        return view('devices.home', [
            'device' => $device,
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
}