<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IotDataController extends Controller
{
    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Incoming IoT Request: ', $request->all());

        // 1. Validasi input (bisa menerima 'distance' atau 'distance_cm')
        try {
            // Validasi Input
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'mac_address' => 'required|string',
                'distance_cm' => 'nullable|integer',
                'distance' => 'nullable|integer', // Backward compatibility
                'rain_intensity_raw' => 'nullable|integer',
                'rain_val' => 'nullable|integer',
                'flood_condition' => 'nullable|string',
                'flood_status' => 'nullable|string',
                'error_type' => 'nullable|string',
                'error_message' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::error('Validation failed: ' . json_encode($validator->errors()));
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 1. Cari perangkat berdasarkan MAC Address
            $device = \App\Models\Device::where('mac_address', $request->mac_address)->first();

            if (!$device) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Perangkat tidak terdaftar'
                ], 404);
            }

            // 1.5 Simpan Error Log jika ada error_type & error_message dari ESP32
            if ($request->has('error_type') && $request->has('error_message')) {
                // Anti-spam error logs (hanya catat jika error yang sama belum dicatat dalam 5 menit terakhir)
                $lastError = \App\Models\ErrorLog::where('device_id', $device->id)
                    ->where('error_type', $request->error_type)
                    ->latest('created_at')
                    ->first();
                
                if (!$lastError || \Carbon\Carbon::parse($lastError->created_at)->diffInMinutes(now()) >= 5) {
                    \App\Models\ErrorLog::create([
                        'device_id' => $device->id,
                        'error_type' => $request->error_type,
                        'message' => $request->error_message,
                        'created_at' => now()
                    ]);
                }
            }

            // 2. Ambil nilai jarak (utamakan distance_cm, fallback ke distance)
            $distance = $request->distance_cm ?? $request->distance; // Default
            
            // 4. Hitung Status Banjir berdasarkan Threshold di Pengaturan
            $floodStatus = 'AMAN'; // Default
            
            // Ambil station_id dari device, jika tidak ada pakai default station 1
            $stationId = $device ? $device->station_id : 1;
            
            $threshold = \App\Models\Threshold::where('station_id', $stationId)
                            ->where('water_min_cm', '<=', $distance)
                            ->where('water_max_cm', '>=', $distance)
                            ->first();
                            
            if (!$threshold) {
                // Fallback ke default station 1 jika station ini belum diatur thresholdnya
                $threshold = \App\Models\Threshold::where('station_id', 1)
                                ->where('water_min_cm', '<=', $distance)
                                ->where('water_max_cm', '>=', $distance)
                                ->first();
            }
            
            if ($threshold) {
                $floodStatus = $threshold->level_label;
            }

            // 5. Simpan data sensor (Map variabel lama ke field database)
            $sensorData = \App\Models\SensorLog::create([
                'device_id' => $device ? $device->id : null,
                'distance_cm' => $distance,
                'water_level_cm' => $request->water_level_cm ?? 0,
                'rain_intensity_raw' => $request->rain_intensity_raw ?? $request->rain_val,
                'flood_condition' => $request->flood_condition ?? 'CERAH',
                'flood_status' => $floodStatus,
            ]);

            $batasAman = 12;
            $batasWaspada = 8;
            $bahayaTh = \App\Models\Threshold::where('station_id', $stationId)->where('level_label', 'BAHAYA')->first();
            $waspadaTh = \App\Models\Threshold::where('station_id', $stationId)->where('level_label', 'WASPADA')->first();
            
            if ($bahayaTh) {
                $batasWaspada = $bahayaTh->water_max_cm;
            }
            if ($waspadaTh) {
                $batasAman = $waspadaTh->water_max_cm;
            }

            // Fetch actuator states
            $actuators = \Illuminate\Support\Facades\Cache::get('actuator_states', [
                'buzzer' => true,
                'pompa' => true,
                'led' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data sensor berhasil disimpan',
                'data' => $sensorData,
                'config' => [
                    'batasAman' => $batasAman,
                    'batasWaspada' => $batasWaspada,
                    'buzzer' => $actuators['buzzer'],
                    'pompa' => $actuators['pompa'],
                    'led' => $actuators['led']
                ]
            ], 201);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving IoT data: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
            ], 500);
        }
    }
}
