<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Station
        $stationId = DB::table('stations')->insertGetId([
            'name' => 'EWS 1',
            'location' => 'Sungai Brantas',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Create Device
        $deviceId = DB::table('devices')->insertGetId([
            'station_id' => $stationId,
            'mac_address' => '00:11:22:33:44:55',
            'name' => 'Node 1',
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Create Thresholds
        DB::table('thresholds')->insert([
            ['station_id' => $stationId, 'level_label' => 'BAHAYA', 'water_min_cm' => 0, 'water_max_cm' => 8, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['station_id' => $stationId, 'level_label' => 'WASPADA', 'water_min_cm' => 8.1, 'water_max_cm' => 12, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['station_id' => $stationId, 'level_label' => 'AMAN', 'water_min_cm' => 12.1, 'water_max_cm' => 100, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ]);

        // 4. Create Sensor Logs & Alerts
        $now = Carbon::now();
        $sensorRecords = [];
        $alertRecords = [];

        for ($i = 100; $i >= 0; $i--) {
            $distance = 25 - (sin($i / 5) * 10) - (rand(-2, 2));
            if ($distance < 5) $distance = 5;
            
            $status = 'Aman';
            if ($distance <= 8) $status = 'Bahaya';
            elseif ($distance <= 12) $status = 'Waspada';

            $rain = ($distance <= 15) ? 'Hujan' : 'Cerah';
            $rain_val = ($rain == 'Hujan') ? rand(500, 1024) : rand(0, 499);
            
            $timestamp = $now->copy()->subMinutes($i * 5);

            $sensorRecords[] = [
                'device_id' => $deviceId,
                'distance_cm' => round($distance, 1),
                'water_level_cm' => 0, // Assuming calculate later or not used heavily
                'rain_intensity_raw' => $rain_val,
                'flood_condition' => $rain,
                'flood_status' => $status,
                'created_at' => $timestamp,
            ];

            // If bahaya or waspada, create an alert roughly 1/3 of the time to avoid too many
            if (($status == 'Bahaya' || $status == 'Waspada') && rand(1, 3) == 1) {
                $alertRecords[] = [
                    'station_id' => $stationId,
                    'alert_level' => $status,
                    'message' => 'Terdeteksi level air ' . round($distance, 1) . ' cm (' . $status . ')',
                    'created_at' => $timestamp,
                ];
            }
        }

        DB::table('sensor_logs')->insert($sensorRecords);
        if (count($alertRecords) > 0) {
            DB::table('alert_history')->insert($alertRecords);
        }
    }
}
