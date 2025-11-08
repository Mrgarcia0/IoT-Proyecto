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
        $tz = config('app.timezone', 'UTC');
        $now = Carbon::now($tz);

        $query = SensorReading::where('device_id', $device->id);

        switch ($range) {
            case 'hour':
                $from = $now->copy()->subHour();
                $query->where('recorded_at', '>=', $from);
                break;
            case 'day':
                // Sólo el día calendario actual en zona horaria de la app
                $from = $now->copy()->startOfDay();
                $to = $now->copy()->endOfDay();
                $query->whereBetween('recorded_at', [$from, $to]);
                break;
            case 'week':
                $from = $now->copy()->subWeek();
                $query->where('recorded_at', '>=', $from);
                break;
            case 'month':
                $from = $now->copy()->subMonth();
                $query->where('recorded_at', '>=', $from);
                break;
            case 'historico':
                // Sin filtro: regresar todo el histórico
                break;
            default:
                // Por defecto: hoy
                $from = $now->copy()->startOfDay();
                $to = $now->copy()->endOfDay();
                $query->whereBetween('recorded_at', [$from, $to]);
        }

        $readings = $query->orderBy('recorded_at', 'asc')
            ->get(['variable_name','value','unit','recorded_at']);

        $series = [];
        $units  = [];
        foreach ($readings as $r) {
            $point = [
                't' => $r->recorded_at->setTimezone($tz)->toIso8601String(),
                'v' => $r->value,
            ];
            $series[$r->variable_name][] = $point;
            if (!isset($units[$r->variable_name])) {
                $units[$r->variable_name] = $r->unit;
            }
        }

        return response()->json([
            'series' => $series,
            'units' => $units,
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

        switch ($device->id) {
            case 1: // Termostato: temperatura y humedad
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
                break;
            case 2: // Consumo eléctrico: potencia (W)
                $power = 150 + 200 * max(0, sin(($hour - 12) / 24 * 2 * $pi)) + (mt_rand(-50, 50) / 10);
                $power = max(50, min(800, $power));
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'power',
                    'value' => round($power, 2),
                    'unit' => 'W',
                    'recorded_at' => $now,
                ]);
                break;
            case 3: // Calidad del aire: CO2 y PM2.5
                $co2 = 450 + 300 * max(0, sin(($hour - 9) / 24 * 2 * $pi)) + (mt_rand(-200, 200) / 10);
                $co2 = max(380, min(1200, $co2));
                $pm25 = 12 + 18 * max(0, sin(($hour - 16) / 24 * 2 * $pi)) + (mt_rand(-200, 200) / 100);
                $pm25 = max(3, min(80, $pm25));
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'co2',
                    'value' => round($co2, 0),
                    'unit' => 'ppm',
                    'recorded_at' => $now,
                ]);
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'pm25',
                    'value' => round($pm25, 2),
                    'unit' => 'µg/m³',
                    'recorded_at' => $now,
                ]);
                break;
            case 4: // Humo y gas: índices
                $smoke = max(0, min(100, 5 + 10 * max(0, sin(($hour - 18)/24*2*$pi)) + (mt_rand(-100,100)/20)));
                $gas   = max(0, min(100, 8 + 12 * max(0, sin(($hour - 18)/24*2*$pi)) + (mt_rand(-100,100)/20)));
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'smoke',
                    'value' => round($smoke, 2),
                    'unit' => 'index',
                    'recorded_at' => $now,
                ]);
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'gas',
                    'value' => round($gas, 2),
                    'unit' => 'index',
                    'recorded_at' => $now,
                ]);
                break;
            case 5: // Polvo: PM10
                $pm10 = 20 + 25 * max(0, sin(($hour - 15)/24*2*$pi)) + (mt_rand(-300,300)/100);
                $pm10 = max(5, min(150, $pm10));
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'pm10',
                    'value' => round($pm10, 2),
                    'unit' => 'µg/m³',
                    'recorded_at' => $now,
                ]);
                break;
            default:
                // Fallback: temperatura
                $temp = 22 + (mt_rand(-200,200)/100);
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'temperature',
                    'value' => round($temp, 2),
                    'unit' => '°C',
                    'recorded_at' => $now,
                ]);
        }

        return response()->json(['ok' => true, 'recorded_at' => $now->toIso8601String()]);
    }
}