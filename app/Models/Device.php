<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function sensorLogs()
    {
        return $this->hasMany(SensorLog::class);
    }

    public function errorLogs()
    {
        return $this->hasMany(ErrorLog::class);
    }
}
