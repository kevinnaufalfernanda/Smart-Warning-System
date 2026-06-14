<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Station;
use App\Models\Device;
use App\Models\Threshold;

$station = Station::firstOrCreate(
    ['id' => 1],
    ['name' => 'EWS 1', 'location' => 'Sungai Brantas Soekarno-Hatta']
);

$device = Device::firstOrCreate(
    ['mac_address' => 'DEFAULT_MAC'],
    ['station_id' => $station->id, 'name' => 'EWS 1', 'is_active' => true]
);

// We will also seed default thresholds if they don't exist
if (Threshold::where('station_id', $station->id)->count() === 0) {
    Threshold::create([
        'station_id' => $station->id,
        'level_label' => 'BAHAYA',
        'water_min_cm' => 0,
        'water_max_cm' => 8
    ]);
    Threshold::create([
        'station_id' => $station->id,
        'level_label' => 'WASPADA',
        'water_min_cm' => 8.01,
        'water_max_cm' => 12
    ]);
    Threshold::create([
        'station_id' => $station->id,
        'level_label' => 'AMAN',
        'water_min_cm' => 12.01,
        'water_max_cm' => 400
    ]);
}

echo "Seeding complete.";
