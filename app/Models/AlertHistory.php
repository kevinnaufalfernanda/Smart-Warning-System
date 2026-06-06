<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertHistory extends Model
{
    use HasFactory;

    protected $table = 'alert_history';
    public $timestamps = false; // Only using created_at
    protected $guarded = [];

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function notificationLogs()
    {
        return $this->hasMany(NotificationLog::class, 'alert_id');
    }
}
