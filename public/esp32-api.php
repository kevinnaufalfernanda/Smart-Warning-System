<?php
/**
 * ESP32 API Bridge
 * File ini diakses langsung oleh ESP32 via IP tanpa melalui routing Herd/Valet.
 * URL: http://<IP_LAPTOP>/esp32-api.php
 */

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Boot the application
$app->boot();

// Set header JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

// Baca JSON body
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

$macAddress = $input['mac_address'] ?? 'DEFAULT_MAC';
$jarak = $input['distance'] ?? 0;
$hujan = $input['rain_val'] ?? 4095;

try {
    // 1. Validasi Device
    $device = \Illuminate\Support\Facades\DB::table('devices')->where('mac_address', $macAddress)->first();
    $deviceId = $device ? $device->id : null;
    $stationId = $device ? $device->station_id : null;

    // 2. Evaluasi Ambang Batas
    $kondisiHujan = ($hujan < 1400) ? 'HUJAN' : 'CERAH';
    $statusJarak = 'AMAN';

    $thresholds = \Illuminate\Support\Facades\DB::table('thresholds')->where('station_id', $stationId)->get();

    if ($thresholds->isNotEmpty()) {
        foreach ($thresholds as $t) {
            if ($jarak >= $t->water_min_cm && $jarak <= $t->water_max_cm) {
                $statusJarak = strtoupper($t->level_label);
            }
        }
    } else {
        if ($jarak <= 20) {
            $statusJarak = 'BAHAYA';
        } elseif ($jarak > 20 && $jarak <= 60) {
            $statusJarak = 'WASPADA';
        } else {
            $statusJarak = 'AMAN';
        }
    }

    // 3. Insert ke sensor_logs
    $logId = \Illuminate\Support\Facades\DB::table('sensor_logs')->insertGetId([
        'device_id' => $deviceId,
        'distance_cm' => $jarak,
        'water_level_cm' => 0,
        'rain_intensity_raw' => $hujan,
        'flood_condition' => $kondisiHujan,
        'flood_status' => $statusJarak,
        'created_at' => now()
    ]);

    // 4. Notifikasi jika darurat
    $kondisiDarurat = ($statusJarak === 'BAHAYA' || $statusJarak === 'WASPADA' || $kondisiHujan === 'HUJAN');

    if ($kondisiDarurat) {
        $lastAlert = \Illuminate\Support\Facades\DB::table('alert_history')
            ->where('station_id', $stationId)
            ->latest('id')
            ->first();

        $bolehKirim = true;
        if ($lastAlert) {
            $lastAlertTime = \Carbon\Carbon::parse($lastAlert->created_at);
            if ($lastAlert->alert_level === $statusJarak && now()->diffInSeconds($lastAlertTime) < 10) {
                $bolehKirim = false;
            }
        }

        if ($bolehKirim) {
            $waktu = now()->format('Y-m-d H:i:s');
            $pesanNotif = "🚨 *PERINGATAN DINI!* 🚨\n\n";
            $pesanNotif .= "Status: *$statusJarak*\n";
            $pesanNotif .= "Ketinggian Air (Jarak): $jarak cm\n";
            $pesanNotif .= "Kondisi Cuaca: $kondisiHujan\n";
            $pesanNotif .= "Waktu: $waktu\n\n";
            $pesanNotif .= "Harap segera ambil tindakan!";

            $alertId = \Illuminate\Support\Facades\DB::table('alert_history')->insertGetId([
                'station_id' => $stationId,
                'alert_level' => $statusJarak,
                'message' => $pesanNotif,
                'created_at' => now()
            ]);

            try {
                \App\Services\NotificationService::sendTelegramMessage($pesanNotif);
                $notifStatus = 'Sent';
            } catch (\Exception $e) {
                $notifStatus = 'Failed';
            }

            \Illuminate\Support\Facades\DB::table('notification_logs')->insert([
                'alert_id' => $alertId,
                'recipient' => 'Telegram Group',
                'platform' => 'Telegram',
                'status' => $notifStatus,
                'sent_at' => now()
            ]);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Data tersimpan dan dievaluasi.',
        'data' => [
            'status' => $statusJarak,
            'cuaca' => $kondisiHujan
        ]
    ]);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}
