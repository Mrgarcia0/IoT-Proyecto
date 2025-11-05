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
            'name' => 'Sensor Humedad Invernadero',
            'type' => 'Digital Twin',
            'location' => 'Invernadero #1',
        ]);

        $device3 = Device::create([
            'name' => 'API Clima Externo',
            'type' => 'API',
            'location' => 'Ciudad',
        ]);

        $device4 = Device::create([
            'name' => 'DataSet Histórico Presión',
            'type' => 'DataSet',
            'location' => 'Laboratorio',
        ]);

        $device5 = Device::create([
            'name' => 'Monitor de Energía Oficina',
            'type' => 'Real',
            'location' => 'Oficina Principal',
            'is_active' => false, // Ejemplo de sensor inactivo
        ]);

        $devices = [$device1, $device2, $device3, $device4, $device5];

        // 2. Generar Lecturas de Sensores para los últimos 7 días
        $now = Carbon::now();
        foreach ($devices as $device) {
            if (!$device->is_active) continue; // No generar datos para sensores inactivos

            for ($d = 7; $d >= 0; $d--) {
                for ($h = 0; $h < 24; $h++) {
                    $recordDate = $now->copy()->subDays($d)->setHour($h)->setMinute(0)->setSecond(0);

                    // Simular Temperatura
                    SensorReading::create([
                        'device_id' => $device->id,
                        'variable_name' => 'temperature',
                        'value' => rand(1800, 2500) / 100, // Valor entre 18.00 y 25.00
                        'unit' => '°C',
                        'recorded_at' => $recordDate
                    ]);

                    // Simular Humedad
                    SensorReading::create([
                        'device_id' => $device->id,
                        'variable_name' => 'humidity',
                        'value' => rand(4000, 6500) / 100, // Valor entre 40.00 y 65.00
                        'unit' => '%',
                        'recorded_at' => $recordDate
                    ]);
                }
            }
        }
    }
}
