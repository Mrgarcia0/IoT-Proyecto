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