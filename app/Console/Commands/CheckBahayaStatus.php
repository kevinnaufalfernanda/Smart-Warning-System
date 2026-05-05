<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
use Carbon\Carbon;

class CheckBahayaStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensor:check-bahaya';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek database untuk status BAHAYA terbaru dan kirim Telegram';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Beri jeda 2 menit untuk mengecek data yang baru masuk
        $satuMenitLalu = Carbon::now()->subMinutes(2)->format('Y-m-d H:i:s');
        
        $dataTerbaru = DB::table('sensor_logs')
            ->where('created_at', '>=', $satuMenitLalu)
            ->where(function ($query) {
                $query->where('flood_status', 'BAHAYA')
                      ->orWhere('flood_condition', 'HUJAN');
            })
            ->latest('id')
            ->first();

        if ($dataTerbaru) {
            $jarak = $dataTerbaru->distance_cm;
            $kondisi = $dataTerbaru->flood_condition;
            $status = $dataTerbaru->flood_status;

            // Jika aplikasi menggunakan UTC, kita sesuaikan ke Asia/Jakarta untuk pesan Telegram
            try {
                $waktu = Carbon::parse($dataTerbaru->created_at)
                    ->timezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $waktu = $dataTerbaru->created_at;
            }
            
            $pesanNotif = "🚨 *PERINGATAN DINI (AUTO-SCAN)!* 🚨\n\n";
            $pesanNotif .= "Status: *$status*\n";
            $pesanNotif .= "Ketinggian Air (Jarak): $jarak cm\n";
            $pesanNotif .= "Kondisi Cuaca: $kondisi\n";
            $pesanNotif .= "Waktu: $waktu\n\n";
            $pesanNotif .= "Harap segera ambil tindakan!";
            
            // Catat ke log alert_history
            $alertId = DB::table('alert_history')->insertGetId([
                'station_id' => null, // Karena cron tidak selalu tau station_id tanpa join
                'alert_level' => $status,
                'message' => $pesanNotif,
                'created_at' => now()
            ]);

            try {
                NotificationService::sendTelegramMessage($pesanNotif);
                $notifStatus = 'Sent';
            } catch (\Exception $e) {
                $notifStatus = 'Failed';
            }

            // Catat ke log notifikasi
            DB::table('notification_logs')->insert([
                'alert_id' => $alertId,
                'recipient' => 'Telegram Group (Auto-Scan)',
                'platform' => 'Telegram',
                'status' => $notifStatus,
                'sent_at' => now()
            ]);

            $this->info("Notifikasi bahaya terkirim!");
        } else {
            $this->info("Aman, tidak ada data bahaya baru dalam 1 menit terakhir.");
        }
    }
}
