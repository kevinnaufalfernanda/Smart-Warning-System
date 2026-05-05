<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\NotificationService;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    $totalData = DB::table('sensor_logs')->count();
    $totalPeringatan = DB::table('sensor_logs')
                        ->whereIn('flood_status', ['BAHAYA', 'WASPADA'])
                        ->count();
    $notifikasi = DB::table('alert_history')->orderBy('id', 'desc')->take(5)->get();
    
    // Asumsi 1 ESP32 untuk sekarang, atau hitung dari devices
    $totalPerangkat = DB::table('devices')->count();
    if ($totalPerangkat == 0) $totalPerangkat = 1; // Fallback jika belum ada device
    
    return view('dashboard', [
        'totalData' => $totalData,
        'totalPeringatan' => $totalPeringatan,
        'totalPerangkat' => $totalPerangkat,
        'notifikasi' => $notifikasi
    ]);
})->name('dashboard');

Route::get('/peringatan', function () {
    $peringatan = DB::table('sensor_logs')
                    ->whereIn('flood_status', ['BAHAYA', 'WASPADA'])
                    ->orderBy('id', 'desc')
                    ->take(200)
                    ->get();
    
    $totalBahaya = DB::table('sensor_logs')->where('flood_status', 'BAHAYA')->count();
    $totalSiaga = DB::table('sensor_logs')->where('flood_status', 'WASPADA')->count();
    
    return view('peringatan', [
        'data' => $peringatan,
        'totalBahaya' => $totalBahaya,
        'totalSiaga' => $totalSiaga
    ]);
})->name('peringatan');

Route::get('/perangkat', function () {
    return view('perangkat');
})->name('perangkat');

Route::get('/riwayat', function () {
    $riwayat = DB::table('sensor_logs')->orderBy('id', 'desc')->take(200)->get();
    
    $totalData = DB::table('sensor_logs')->count();
    
    $avgLevel = DB::table('sensor_logs')->avg('distance_cm');
    $avgLevel = $avgLevel ? round($avgLevel) : 0;
    
    $highestLevel = DB::table('sensor_logs')->max('distance_cm');
    $highestLevel = $highestLevel ? $highestLevel : 0;
    
    return view('riwayat', [
        'data' => $riwayat,
        'totalData' => $totalData,
        'avgLevel' => $avgLevel,
        'highestLevel' => $highestLevel
    ]);
})->name('riwayat');

Route::get('/pengaturan', function () {
    return view('pengaturan');
})->name('pengaturan');

Route::get('/data-sensor/latest', function () {
    $data = DB::table('sensor_logs')->latest('id')->first();
    
    $jarak = $data ? $data->distance_cm : 0;
    // flood_condition contains 'HUJAN' or 'CERAH'
    $kondisiHujan = 'Cerah';
    if ($data) {
        if (strtoupper($data->flood_condition) === 'HUJAN') {
            $kondisiHujan = 'Hujan';
        } elseif ($data->rain_intensity_raw !== null && $data->rain_intensity_raw < 1400) {
            $kondisiHujan = 'Hujan';
        }
    }
    
    // Prepare dummy raw_pesan to avoid breaking frontend if it relies on it
    $rawPesan = $data ? "Jarak:{$data->distance_cm}, Hujan:{$data->rain_intensity_raw}, Kondisi:{$data->flood_condition}, Status:{$data->flood_status}" : '';
    
    return response()->json([
        'jarak' => $jarak,
        'hujan' => $kondisiHujan,
        'waktu' => $data ? $data->created_at : null,
        'raw_pesan' => $rawPesan
    ]);
});

Route::post('/api/trigger-notif', function (Request $request) {
    // Terima JSON POST
    $macAddress = $request->input('mac_address');
    $jarak = $request->input('distance');
    $hujan = $request->input('rain_val'); 
    
    // Fallback GET parameter jika ESP32 belum diubah
    if (!$macAddress) $macAddress = $request->query('mac_address', 'DEFAULT_MAC');
    if ($jarak === null) $jarak = $request->input('jarak', $request->query('jarak', 0));
    if ($hujan === null) $hujan = $request->input('hujan', $request->query('hujan', 4095));

    // 1. Validasi Device
    $device = DB::table('devices')->where('mac_address', $macAddress)->first();
    $deviceId = $device ? $device->id : null;
    $stationId = $device ? $device->station_id : null;
    
    // 2. Evaluasi Ambang Batas (Thresholds)
    $kondisiHujan = ($hujan < 1400) ? 'HUJAN' : 'CERAH';
    $statusJarak = 'AMAN';
    
    // Ambil threshold dari DB jika ada
    $thresholds = DB::table('thresholds')->where('station_id', $stationId)->get();
    
    if ($thresholds->isNotEmpty()) {
        foreach ($thresholds as $t) {
            if ($jarak >= $t->water_min_cm && $jarak <= $t->water_max_cm) {
                $statusJarak = strtoupper($t->level_label);
            }
        }
    } else {
        // Fallback default rules
        if ($jarak <= 20) {
            $statusJarak = 'BAHAYA';
        } elseif ($jarak > 20 && $jarak <= 60) {
            $statusJarak = 'WASPADA';
        } else {
            $statusJarak = 'AMAN';
        }
    }
    
    // 3. Insert ke sensor_logs
    $logId = DB::table('sensor_logs')->insertGetId([
        'device_id' => $deviceId,
        'distance_cm' => $jarak,
        'water_level_cm' => 0,
        'rain_intensity_raw' => $hujan,
        'flood_condition' => $kondisiHujan,
        'flood_status' => $statusJarak,
        'created_at' => now()
    ]);
    
    // 4. Notifikasi jika BAHAYA / WASPADA / HUJAN
    $kondisiDarurat = ($statusJarak === 'BAHAYA' || $statusJarak === 'WASPADA' || $kondisiHujan === 'HUJAN');
    
    if ($kondisiDarurat) {
        // Cek Anti-Spam (Cooldown): Kapan terakhir kali stasiun ini mengirim notifikasi?
        $lastAlert = DB::table('alert_history')
            ->where('station_id', $stationId)
            ->latest('id')
            ->first();
            
        $bolehKirim = true;
        if ($lastAlert) {
            $lastAlertTime = \Carbon\Carbon::parse($lastAlert->created_at);
            // Cooldown 10 detik, TAPI ABAIKAN cooldown jika statusnya BERUBAH (misal BAHAYA turun ke WASPADA)
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
            
            // Catat ke alert_history
            $alertId = DB::table('alert_history')->insertGetId([
                'station_id' => $stationId,
                'alert_level' => $statusJarak,
                'message' => $pesanNotif,
                'created_at' => now()
            ]);
            
            // Kirim Telegram
            try {
                NotificationService::sendTelegramMessage($pesanNotif);
                $notifStatus = 'Sent';
            } catch (\Exception $e) {
                $notifStatus = 'Failed';
            }
            
            // Catat ke notification_logs (HANYA TELEGRAM)
            DB::table('notification_logs')->insert([
                'alert_id' => $alertId,
                'recipient' => 'Telegram Group',
                'platform' => 'Telegram',
                'status' => $notifStatus,
                'sent_at' => now()
            ]);
        }
    }
    
    return response()->json([
        'success' => true, 
        'message' => 'Data tersimpan dan dievaluasi.',
        'data' => [
            'status' => $statusJarak,
            'cuaca' => $kondisiHujan
        ]
    ]);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

