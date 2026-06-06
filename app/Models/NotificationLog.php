<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    public $timestamps = false; // Only using sent_at
    protected $guarded = [];

    public function alertHistory()
    {
        return $this->belongsTo(AlertHistory::class, 'alert_id');
    }
}
