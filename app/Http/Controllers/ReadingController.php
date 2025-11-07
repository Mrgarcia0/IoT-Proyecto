<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReadingController extends Controller
{
    /**
     * Devuelve lecturas del dispositivo en el rango solicitado.
     * ?range=hour|day|week|month
     */
    public function readings(Request $request, Device $device)
    {
        $range = $request->string('range')->toString();
        $now = Carbon::now();

        switch ($range) {
            case 'hour':
                $from = $now->copy()->subHour();
                break;
            case 'day':
                $from = $now->copy()->subDay();
                break;
            case 'week':
                $from = $now->copy()->subWeek();
                break;
            case 'month':
                $from = $now->copy()->subMonth();
                break;
            default:
                $from = $now->copy()->subDay();
        }

        $readings = SensorReading::where('device_id', $device->id)
            ->where('recorded_at', '>=', $from)
            ->orderBy('recorded_at', 'asc')
            ->get(['variable_name','value','unit','recorded_at']);

        $temperature = [];
        $humidity = [];

        foreach ($readings as $r) {
            $point = [
                't' => $r->recorded_at->toIso8601String(),
                'v' => $r->value,
            ];
            if ($r->variable_name === 'temperature') {
                $temperature[] = $point;
            } elseif ($r->variable_name === 'humidity') {
                $humidity[] = $point;
            }
        }

        return response()->json([
            'temperature' => $temperature,
            'humidity' => $humidity,
        ]);
    }

    /**
     * Inserta una muestra simulada (temperatura + humedad) para el dispositivo.
     */
    public function simulateSample(Device $device)
    {
        // No generar muestras si el dispositivo está apagado
        if (!$device->is_active) {
            return response()->json(['ok' => false, 'reason' => 'device_off'], 200);
        }
        $now = Carbon::now();
        $hour = (int) $now->format('G');
        $pi = pi();
        // Mismo modelo diurno que el seeder para coherencia visual
        $temp = 24 + 4 * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-20, 20) / 100);
        $temp = max(18, min(30, $temp));

        $hum = 70 - 12 * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-150, 150) / 100);
        $hum = max(45, min(90, $hum));

        SensorReading::create([
            'device_id' => $device->id,
            'variable_name' => 'temperature',
            'value' => round($temp, 2),
            'unit' => '°C',
            'recorded_at' => $now,
        ]);

        SensorReading::create([
            'device_id' => $device->id,
            'variable_name' => 'humidity',
            'value' => round($hum, 2),
            'unit' => '%',
            'recorded_at' => $now,
        ]);

        return response()->json(['ok' => true, 'recorded_at' => $now->toIso8601String()]);
    }
}