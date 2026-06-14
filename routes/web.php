<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\NotificationService;

use App\Http\Controllers\AuthController;
use App\Models\SensorLog;
use App\Models\Device;
use App\Models\AlertHistory;
use App\Models\ErrorLog;
use App\Models\Threshold;
use App\Models\NotificationLog;

Route::get('/setup-db', function() {
    DB::statement("
        CREATE TABLE IF NOT EXISTS `users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `name` varchar(100) NOT NULL,
          `email` varchar(100) NOT NULL,
          `password` varchar(255) NOT NULL,
          `remember_token` varchar(100) DEFAULT NULL,
          `role` enum('Admin','Petugas','Warga') NOT NULL DEFAULT 'Warga',
          `phone_number` varchar(20) DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `email_unik` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    return "Tabel users berhasil dibuat! Silakan kembali ke halaman register.";
});

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        $totalData = SensorLog::count();
        $totalPeringatan = SensorLog::whereIn('flood_status', ['BAHAYA', 'WASPADA'])->count();
        $notifikasi = AlertHistory::orderBy('id', 'desc')->take(5)->get();
        
        $totalPerangkat = Device::count() ?: 1; // Fallback jika belum ada device
        
        return view('dashboard', [
            'totalData' => $totalData,
            'totalPeringatan' => $totalPeringatan,
            'totalPerangkat' => $totalPerangkat,
            'notifikasi' => $notifikasi
        ]);
    })->name('dashboard');

    Route::get('/peringatan', function () {
        $peringatan = SensorLog::with('device.station')
                        ->whereIn('flood_status', ['BAHAYA', 'WASPADA'])
                        ->orderBy('id', 'desc')
                        ->take(200)
                        ->get();
        
        $totalBahaya = SensorLog::where('flood_status', 'BAHAYA')->count();
        $totalSiaga = SensorLog::where('flood_status', 'WASPADA')->count();
        
        return view('peringatan', [
            'data' => $peringatan,
            'totalBahaya' => $totalBahaya,
            'totalSiaga' => $totalSiaga
        ]);
    })->name('peringatan');

    Route::get('/perangkat', function () {
        $errorLogs = [];
        try {
            $errorLogs = ErrorLog::with('device.station')->orderBy('id', 'desc')->take(20)->get();
        } catch (\Exception $e) {}
        
        $devices = \App\Models\Device::with('station')->get();
        
        return view('perangkat', ['errorLogs' => $errorLogs, 'devices' => $devices]);
    })->name('perangkat');

    Route::post('/perangkat', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'mac_address' => 'required|string|max:255|unique:devices,mac_address',
        ]);

        $station = \App\Models\Station::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);

        \App\Models\Device::create([
            'station_id' => $station->id,
            'name' => $request->name,
            'mac_address' => $request->mac_address,
            'is_active' => true,
        ]);

        return back()->with('success', 'Perangkat berhasil ditambahkan!');
    })->name('perangkat.store');

    Route::get('/riwayat', function () {
        $riwayat = SensorLog::with('device.station')->orderBy('id', 'desc')->take(200)->get();
        
        $totalData = SensorLog::count();
        
        $avgLevel = SensorLog::avg('distance_cm') ?? 0;
        $avgLevel = round($avgLevel);
        
        $highestLevel = SensorLog::max('distance_cm') ?? 0;
        
        return view('riwayat', [
            'data' => $riwayat,
            'totalData' => $totalData,
            'avgLevel' => $avgLevel,
            'highestLevel' => $highestLevel
        ]);
    })->name('riwayat');

    Route::get('/pengaturan', function () {
        $stations = \App\Models\Station::with('thresholds')->get();
        return view('pengaturan', ['stations' => $stations]);
    })->name('pengaturan');

    Route::post('/pengaturan/threshold', function (Request $request) {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'batas_aman' => 'required|numeric',
            'batas_waspada' => 'required|numeric',
            'batas_bahaya' => 'required|numeric',
        ]);
        
        $stationId = $request->station_id;
        $aman = $request->batas_aman;
        $waspada = $request->batas_waspada;
        $bahaya = $request->batas_bahaya;
        
        if (!($bahaya <= $waspada && $waspada <= $aman)) {
            return back()->with('error', 'Konfigurasi tidak valid. Pastikan Bahaya <= Waspada <= Aman.');
        }

        \App\Models\Threshold::where('station_id', $stationId)->delete();
        
        \App\Models\Threshold::create(['station_id' => $stationId, 'level_label' => 'BAHAYA', 'water_min_cm' => 0, 'water_max_cm' => $bahaya]);
        \App\Models\Threshold::create(['station_id' => $stationId, 'level_label' => 'WASPADA', 'water_min_cm' => $bahaya + 0.01, 'water_max_cm' => $waspada]);
        \App\Models\Threshold::create(['station_id' => $stationId, 'level_label' => 'AMAN', 'water_min_cm' => $waspada + 0.01, 'water_max_cm' => 400]);

        return back()->with('success', 'Threshold berhasil diperbarui!');
    })->name('pengaturan.threshold');
});

Route::get('/data-sensor/latest', function () {
    $data = SensorLog::latest('id')->first();
    
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
        'raw_pesan' => $rawPesan,
        'status' => $data ? ucfirst(strtolower($data->flood_status)) : 'Aman'
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

    // Validasi Anomali Data (Error Logging)
    if ($jarak < 0 || $jarak > 400) {
        try {
            ErrorLog::create([
                'device_id' => null, // or resolve device first, but for simplicity
                'error_type' => 'Sensor Anomaly',
                'message' => "Jarak tidak valid dibaca oleh sensor: {$jarak} cm. Harus di antara 0-400 cm.",
                'created_at' => now()
            ]);
        } catch (\Exception $e) {}
        
        return response()->json([
            'success' => false,
            'message' => 'Data ditolak: Jarak tidak valid (error logged).'
        ], 400);
    }

    // 1. Validasi Device
    $device = Device::where('mac_address', $macAddress)->first();
    
    // Auto-register jika device belum ada di database
    if (!$device) {
        $station = \App\Models\Station::firstOrCreate(
            ['name' => 'Stasiun EWS Baru'], 
            ['location' => 'Belum Diatur']
        );
        $device = \App\Models\Device::create([
            'mac_address' => $macAddress,
            'name' => 'EWS Baru (' . substr($macAddress, -5) . ')',
            'station_id' => $station->id,
            'is_active' => true
        ]);
    }

    $deviceId = $device->id;
    $stationId = $device->station_id;
    
    // 2. Evaluasi Ambang Batas (Thresholds)
    $kondisiHujan = ($hujan < 1400) ? 'HUJAN' : 'CERAH';
    $statusJarak = 'AMAN';
    
    // Ambil threshold dari DB jika ada
    $thresholds = Threshold::where('station_id', $stationId)->get();
    
    if ($thresholds->isNotEmpty()) {
        foreach ($thresholds as $t) {
            if ($jarak >= $t->water_min_cm && $jarak <= $t->water_max_cm) {
                $statusJarak = strtoupper($t->level_label);
            }
        }
    } else {
        // Fallback default rules
        if ($jarak <= 8) {
            $statusJarak = 'BAHAYA';
        } elseif ($jarak > 8 && $jarak <= 12) {
            $statusJarak = 'WASPADA';
        } else {
            $statusJarak = 'AMAN';
        }
    }
    
    // 3. Insert ke sensor_logs
    $log = SensorLog::create([
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
        $lastAlert = AlertHistory::where('station_id', $stationId)->latest('id')->first();
            
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
            $alert = AlertHistory::create([
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
                try {
                    ErrorLog::create([
                        'device_id' => $deviceId,
                        'error_type' => 'Telegram Notification Failed',
                        'message' => $e->getMessage(),
                        'created_at' => now()
                    ]);
                } catch (\Exception $dbEx) {}
            }
            
            // Catat ke notification_logs (HANYA TELEGRAM)
            NotificationLog::create([
                'alert_id' => $alert->id,
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

