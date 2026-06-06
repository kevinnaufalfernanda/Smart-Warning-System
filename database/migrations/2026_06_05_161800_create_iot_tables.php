<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Stations (Stasiun Pemantau)
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Devices (Perangkat EWS)
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->nullable()->constrained('stations')->nullOnDelete();
            $table->string('mac_address')->unique();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Thresholds (Ambang Batas per Stasiun)
        Schema::create('thresholds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('stations')->cascadeOnDelete();
            $table->float('water_min_cm');
            $table->float('water_max_cm');
            $table->string('level_label'); // AMAN, WASPADA, BAHAYA
            $table->timestamps();
        });

        // 4. Tabel Sensor Logs (Riwayat Pembacaan Sensor)
        Schema::create('sensor_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->float('distance_cm');
            $table->float('water_level_cm')->default(0);
            $table->integer('rain_intensity_raw')->nullable();
            $table->string('flood_condition')->default('CERAH'); // HUJAN / CERAH
            $table->string('flood_status')->default('AMAN'); // AMAN, WASPADA, BAHAYA
            $table->timestamp('created_at')->useCurrent();
            // Note: tidak menggunakan $table->timestamps() agar hemat ukuran
        });

        // 5. Tabel Alert History (Riwayat Peringatan Dini)
        Schema::create('alert_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->nullable()->constrained('stations')->cascadeOnDelete();
            $table->string('alert_level');
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();
        });

        // 6. Tabel Notification Logs (Catatan Pengiriman Notifikasi)
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained('alert_history')->cascadeOnDelete();
            $table->string('recipient')->nullable();
            $table->string('platform')->default('Telegram');
            $table->string('status')->default('Sent'); // Sent, Failed
            $table->timestamp('sent_at')->useCurrent();
        });

        // 7. Tabel Error Logs (Catatan Anomali Sistem)
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
            $table->string('error_type');
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('alert_history');
        Schema::dropIfExists('sensor_logs');
        Schema::dropIfExists('thresholds');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('stations');
    }
};
