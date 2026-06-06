<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorLog extends Model
{
    use HasFactory;

    public $timestamps = false; // Only using created_at
    protected $guarded = [];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
