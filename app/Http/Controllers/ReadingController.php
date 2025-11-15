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
    public function readings(Request $request, $device)
    {
        // Obtener el rango de forma segura y compatible
        $range = (string) $request->query('range', 'day');
        $tz = config('app.timezone', 'UTC');
        $now = Carbon::now($tz);

        $deviceId = (int) $device;
        // Intentar cargar el dispositivo (si la BD está disponible), de lo contrario usar stub
        $deviceModel = null;
        try { $deviceModel = Device::find($deviceId); } catch (\Throwable $e) { $deviceModel = null; }
        $settings = $deviceModel && $deviceModel->settings ? json_decode($deviceModel->settings, true) : [];

        // Importante: para el Termostato (id=1) intentamos primero la BD.
        // Si la BD falla, el catch más abajo devuelve un fallback en memoria.

        try {
            $query = SensorReading::where('device_id', $deviceId);

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

        // Fallback específico: si el Termostato (id=1) no tiene lecturas en el rango,
        // insertamos una muestra inmediata para evitar que el explorador quede vacío.
        if ($readings->isEmpty() && $deviceId === 1) {
            // Generar una muestra y volver a consultar.
            // Usamos la misma lógica que simulateSample para el caso 1.
            $pi = pi();
            $hour = (int) $now->format('G');
            $target = isset($settings['temperature_target']) ? (float) $settings['temperature_target'] : 24.0;
            $rangeAmp = 4.0;
            if ($target < 20) { $rangeAmp = 3.0; }
            if ($target < 18) { $rangeAmp = 2.5; }
            $temp = $target + $rangeAmp * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-20, 20) / 100);
            $min = max(12, $target - ($rangeAmp + 2));
            $max = min(32, $target + ($rangeAmp + 2));
            $temp = max($min, min($max, $temp));
            $hum = 70 - 12 * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-150, 150) / 100);
            $hum = max(45, min(90, $hum));
            SensorReading::create([
                'device_id' => $deviceId,
                'variable_name' => 'temperature',
                'value' => round($temp, 2),
                'unit' => '°C',
                'recorded_at' => $now,
            ]);
            SensorReading::create([
                'device_id' => $deviceId,
                'variable_name' => 'humidity',
                'value' => round($hum, 2),
                'unit' => '%',
                'recorded_at' => $now,
            ]);
            $readings = $query->orderBy('recorded_at', 'asc')
                ->get(['variable_name','value','unit','recorded_at']);
        }
        } catch (\Throwable $e) {
            // Fallback extremo: si hay error de BD (p.ej., sin .env),
            // devolvemos una serie simulada en memoria SOLO para el Termostato (id=1).
            if ($deviceId === 1) {
                $pi = pi();
                $hour = (int) $now->format('G');
                $target = isset($settings['temperature_target']) ? (float) $settings['temperature_target'] : 24.0;
                $rangeAmp = 4.0;
                if ($target < 20) { $rangeAmp = 3.0; }
                if ($target < 18) { $rangeAmp = 2.5; }
                $temp = $target + $rangeAmp * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-20, 20) / 100);
                $min = max(12, $target - ($rangeAmp + 2));
                $max = min(32, $target + ($rangeAmp + 2));
                $temp = max($min, min($max, $temp));
                $hum = 70 - 12 * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-150, 150) / 100);
                $hum = max(45, min(90, $hum));
                return response()->json([
                    'series' => [
                        'temperature' => [['t' => $now->toIso8601String(), 'v' => round($temp, 2)]],
                        'humidity'    => [['t' => $now->toIso8601String(), 'v' => round($hum, 2)]],
                    ],
                    'units' => [
                        'temperature' => '°C',
                        'humidity' => '%',
                    ],
                ]);
            }
            // Para otros dispositivos, mantener el error original para no alterar comportamiento.
            throw $e;
        }

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
     * Devuelve las últimas lecturas por variable del dispositivo.
     */
    public function latest(Device $device)
    {
        $rows = SensorReading::where('device_id', $device->id)
            ->orderByDesc('recorded_at')
            ->get(['variable_name','value','unit','recorded_at']);

        $latest = [];
        foreach ($rows as $row) {
            if (!isset($latest[$row->variable_name])) {
                $latest[$row->variable_name] = [
                    'value' => $row->value,
                    'unit' => $row->unit,
                    't' => $row->recorded_at->toIso8601String(),
                ];
            }
        }
        $settings = $device->settings ? json_decode($device->settings, true) : [];
        return response()->json([
            'series' => $latest,
            'settings' => $settings,
            'is_active' => (bool) $device->is_active,
        ]);
    }

    /**
     * Inserta una muestra simulada (temperatura + humedad) para el dispositivo.
     */
    public function simulateSample(Device $device)
    {
        // Generar muestras siempre para alimentar el tablero.
        // Los actuadores controlan sus propias salidas (p.ej., TV/Nevera/Gas pueden valer 0 si están apagados).
        $now = Carbon::now();
        $hour = (int) $now->format('G');
        $pi = pi();

        $settings = $device->settings ? json_decode($device->settings, true) : [];

        switch ($device->id) {
            case 1: // Termostato: temperatura y humedad
                // Ajuste por settings: temperature_target desplaza el centro y estrecha o amplia el rango
                $target = isset($settings['temperature_target']) ? (float) $settings['temperature_target'] : 24.0;
                $range = 4.0; // amplitud base
                // Si la meta es baja (<20), estrechamos y desplazamos a valores más fríos
                if ($target < 20) { $range = 3.0; }
                if ($target < 18) { $range = 2.5; }
                $temp = $target + $range * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-20, 20) / 100);
                // límites dinámicos aproximados: alrededor de target ± (range+margin)
                $min = max(12, $target - ($range + 2));
                $max = min(32, $target + ($range + 2));
                $temp = max($min, min($max, $temp));
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
            case 2: // Consumo eléctrico y actuadores de sala/cocina
                $profile = $settings['power_profile'] ?? 'normal';
                // Estados de actuadores
                $tvOn = (bool)($settings['tv_on'] ?? true);
                $fridgeOn = (bool)($settings['fridge_on'] ?? true);
                $lightLiving = (int)($settings['living_light_level'] ?? 40); // %
                $lightKitchen = (int)($settings['kitchen_light_level'] ?? 25); // %
                $lightBath = (int)($settings['bath_light_level'] ?? 0); // %

                // Potencia general (incluye contribuciones)
                $base = $profile === 'eco' ? 80 : ($profile === 'high' ? 160 : 120);
                $amp  = $profile === 'eco' ? 120 : ($profile === 'high' ? 240 : 180);
                $power = $base + $amp * max(0, sin(($hour - 12) / 24 * 2 * $pi)) + (mt_rand(-50, 50) / 10);
                $power = max(30, min(900, $power));

                // TV (W) - apagada => 0
                $tvBase = $tvOn ? 80 : 0;
                $tvAmp  = $tvOn ? 120 : 0;
                $tvPower = $tvOn ? ($tvBase + $tvAmp * max(0, sin(($hour - 20)/24*2*$pi)) + (mt_rand(-40,40)/10)) : 0;
                $tvPower = max($tvOn ? 60 : 0, min($tvOn ? 250 : 0, $tvPower));

                // Nevera (W) - apagada => 0
                $fridgeBase = $fridgeOn ? 60 : 0;
                $fridgeAmp  = $fridgeOn ? 40 : 0;
                $fridgePower = $fridgeOn ? ($fridgeBase + $fridgeAmp * max(0, sin(($hour - 6)/24*2*$pi)) + (mt_rand(-20,20)/10)) : 0;
                $fridgePower = max($fridgeOn ? 40 : 0, min($fridgeOn ? 140 : 0, $fridgePower));

                // Luces (%), ligeras variaciones alrededor del nivel fijado
                $jitter = function($level){ return max(0, min(100, $level + (mt_rand(-80,80)/10))); };
                $lightLivingVar = $jitter($lightLiving);
                $lightKitchenVar = $jitter($lightKitchen);
                $lightBathVar = $jitter($lightBath);

                // Escrituras
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'power',
                    'value' => round($power, 2),
                    'unit' => 'W',
                    'recorded_at' => $now,
                ]);
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'tv_power',
                    'value' => round($tvPower, 2),
                    'unit' => 'W',
                    'recorded_at' => $now,
                ]);
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'fridge_power',
                    'value' => round($fridgePower, 2),
                    'unit' => 'W',
                    'recorded_at' => $now,
                ]);
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'light_living',
                    'value' => round($lightLivingVar, 0),
                    'unit' => '%',
                    'recorded_at' => $now,
                ]);
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'light_kitchen',
                    'value' => round($lightKitchenVar, 0),
                    'unit' => '%',
                    'recorded_at' => $now,
                ]);
                SensorReading::create([
                    'device_id' => $device->id,
                    'variable_name' => 'light_bath',
                    'value' => round($lightBathVar, 0),
                    'unit' => '%',
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
                $valveOpen = (bool) ($settings['gas_valve_open'] ?? true);
                $smoke = max(0, min(100, 5 + 10 * max(0, sin(($hour - 18)/24*2*$pi)) + (mt_rand(-100,100)/20)));
                // Válvula cerrada => gas 0
                $gasBase = $valveOpen ? 8 : 0;
                $gasAmp  = $valveOpen ? 12 : 0;
                $gas   = $valveOpen ? max(0, min(100, $gasBase + $gasAmp * max(0, sin(($hour - 18)/24*2*$pi)) + (mt_rand(-100,100)/20))) : 0;
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