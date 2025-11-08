<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Device;
use App\Models\SensorReading;
use Carbon\Carbon;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Deshabilitar chequeo de claves foráneas
        Schema::disableForeignKeyConstraints();

        // Limpiar tablas para evitar duplicados
        SensorReading::truncate();
        Device::truncate();

        // Volver a habilitar el chequeo
        Schema::enableForeignKeyConstraints();

        $deviceTypes = ['Real', 'Digital Twin', 'API', 'DataSet'];

        // 1. Crear Dispositivos
        $device1 = Device::create([
            'name' => 'Termostato Sala Principal',
            'type' => 'Real',
            'location' => 'Sala de Estar',
            'settings' => json_encode(['calibration_offset' => 0.5]),
            'is_active' => false
        ]);

        $device2 = Device::create([
            'name' => 'Sensor de consumo eléctrico',
            'type' => 'Digital Twin',
            'location' => 'Wattorímetro',
            'is_active' => false,
        ]);

        $device3 = Device::create([
            'name' => 'Sensor de calidad del aire',
            'type' => 'API',
            'location' => 'Entrada de la casa',
            'is_active' => false,
        ]);

        $device4 = Device::create([
            'name' => 'Sensor de humo y gas',
            'type' => 'DataSet',
            'location' => 'Cocina',
            'is_active' => false,
        ]);

        $device5 = Device::create([
            'name' => 'Sensor de polvo',
            'type' => 'Real',
            'location' => 'Casa',
            'is_active' => false, // Ejemplo de sensor inactivo
        ]);

        // 2. Generar lecturas históricas extensas para todos los sensores (población inicial)
        $now = Carbon::now();
        // Últimos 30 días, cada 15 minutos
        for ($d = 30; $d >= 0; $d--) {
            for ($hh = 0; $hh < 24; $hh++) {
                for ($m = 0; $m < 60; $m += 15) {
                    $recordDate = $now->copy()->subDays($d)->setHour($hh)->setMinute($m)->setSecond(0);
                    $hour = (int) $recordDate->format('G');
                    $pi = pi();
                    // Termostato: temperatura y humedad
                    $temp = 24 + 4 * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-20, 20) / 100);
                    $temp = max(18, min(30, $temp));
                    $hum = 70 - 12 * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-150, 150) / 100);
                    $hum = max(45, min(90, $hum));
                    SensorReading::create([
                        'device_id' => $device1->id,
                        'variable_name' => 'temperature',
                        'value' => round($temp, 2),
                        'unit' => '°C',
                        'recorded_at' => $recordDate,
                    ]);
                    SensorReading::create([
                        'device_id' => $device1->id,
                        'variable_name' => 'humidity',
                        'value' => round($hum, 2),
                        'unit' => '%',
                        'recorded_at' => $recordDate,
                    ]);

                    // Consumo eléctrico: potencia (W)
                    $power = 150 + 200 * max(0, sin(($hour - 12) / 24 * 2 * $pi)) + (mt_rand(-50, 50) / 10);
                    $power = max(50, min(800, $power));
                    SensorReading::create([
                        'device_id' => $device2->id,
                        'variable_name' => 'power',
                        'value' => round($power, 2),
                        'unit' => 'W',
                        'recorded_at' => $recordDate,
                    ]);

                    // Calidad del aire: CO2 (ppm) y PM2.5 (µg/m³)
                    $co2 = 450 + 300 * max(0, sin(($hour - 9) / 24 * 2 * $pi)) + (mt_rand(-200, 200) / 10);
                    $co2 = max(380, min(1200, $co2));
                    $pm25 = 12 + 18 * max(0, sin(($hour - 16) / 24 * 2 * $pi)) + (mt_rand(-200, 200) / 100);
                    $pm25 = max(3, min(80, $pm25));
                    SensorReading::create([
                        'device_id' => $device3->id,
                        'variable_name' => 'co2',
                        'value' => round($co2, 0),
                        'unit' => 'ppm',
                        'recorded_at' => $recordDate,
                    ]);
                    SensorReading::create([
                        'device_id' => $device3->id,
                        'variable_name' => 'pm25',
                        'value' => round($pm25, 2),
                        'unit' => 'µg/m³',
                        'recorded_at' => $recordDate,
                    ]);

                    // Humo y gas: índices (0-100)
                    $smoke = max(0, min(100, 5 + 10 * max(0, sin(($hour - 18)/24*2*$pi)) + (mt_rand(-100,100)/20)));
                    $gas   = max(0, min(100, 8 + 12 * max(0, sin(($hour - 18)/24*2*$pi)) + (mt_rand(-100,100)/20)));
                    SensorReading::create([
                        'device_id' => $device4->id,
                        'variable_name' => 'smoke',
                        'value' => round($smoke, 2),
                        'unit' => 'index',
                        'recorded_at' => $recordDate,
                    ]);
                    SensorReading::create([
                        'device_id' => $device4->id,
                        'variable_name' => 'gas',
                        'value' => round($gas, 2),
                        'unit' => 'index',
                        'recorded_at' => $recordDate,
                    ]);

                    // Polvo: PM10 (µg/m³)
                    $pm10 = 20 + 25 * max(0, sin(($hour - 15)/24*2*$pi)) + (mt_rand(-300,300)/100);
                    $pm10 = max(5, min(150, $pm10));
                    SensorReading::create([
                        'device_id' => $device5->id,
                        'variable_name' => 'pm10',
                        'value' => round($pm10, 2),
                        'unit' => 'µg/m³',
                        'recorded_at' => $recordDate,
                    ]);
                }
            }
        }
    }
}
