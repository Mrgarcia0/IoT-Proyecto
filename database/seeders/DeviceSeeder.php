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
            'settings' => json_encode(['calibration_offset' => 0.5])
        ]);

        $device2 = Device::create([
            'name' => 'Sensor de consumo eléctrico',
            'type' => 'Digital Twin',
            'location' => 'wattorímetro',
        ]);

        $device3 = Device::create([
            'name' => 'Sensor de calidad del aire',
            'type' => 'API',
            'location' => 'Entrada de la casa',
        ]);

        $device4 = Device::create([
            'name' => 'Sensor de humo y gas',
            'type' => 'DataSet',
            'location' => 'Cocina',
        ]);

        $device5 = Device::create([
            'name' => 'Sensor de polvo',
            'type' => 'Real',
            'location' => 'Casa',
            'is_active' => false, // Ejemplo de sensor inactivo
        ]);

        // 2. Generar lecturas históricas extensas SOLO para el Termostato (población inicial)
        $now = Carbon::now();
        $device = $device1; // Termostato Sala Principal
        // Últimos 30 días, cada 15 minutos
        for ($d = 30; $d >= 0; $d--) {
            for ($hh = 0; $hh < 24; $hh++) {
                for ($m = 0; $m < 60; $m += 15) {
                    $recordDate = $now->copy()->subDays($d)->setHour($hh)->setMinute($m)->setSecond(0);
                    $hour = (int) $recordDate->format('G');
                    $pi = pi();
                    $temp = 24 + 4 * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-20, 20) / 100);
                    $temp = max(18, min(30, $temp));
                    $hum = 70 - 12 * sin(($hour - 14) / 24 * 2 * $pi) + (mt_rand(-150, 150) / 100);
                    $hum = max(45, min(90, $hum));

                    SensorReading::create([
                        'device_id' => $device->id,
                        'variable_name' => 'temperature',
                        'value' => round($temp, 2),
                        'unit' => '°C',
                        'recorded_at' => $recordDate,
                    ]);
                    SensorReading::create([
                        'device_id' => $device->id,
                        'variable_name' => 'humidity',
                        'value' => round($hum, 2),
                        'unit' => '%',
                        'recorded_at' => $recordDate,
                    ]);
                }
            }
        }
    }
}
