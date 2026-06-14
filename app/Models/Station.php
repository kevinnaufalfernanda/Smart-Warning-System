<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $table = 'stations';
    protected $guarded = [];

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function thresholds()
    {
        return $this->hasMany(Threshold::class);
    }

    public function alertHistories()
    {
        return $this->hasMany(AlertHistory::class);
    }
}
