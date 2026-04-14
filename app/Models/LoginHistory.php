<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginHistory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'guard',
        'logged_in_at',
        'logged_out_at',
    ];

    protected $casts = [
        'successful'    => 'boolean',
        'logged_in_at'  => 'datetime',
        'logged_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDeviceTypeAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (preg_match('/iPad|Tablet/i', $ua))        return 'Tablet';
        if (preg_match('/Mobile|Android|iPhone/i', $ua)) return 'Mobile';
        if (preg_match('/Macintosh|Mac OS/i', $ua))   return 'Mac';
        if (preg_match('/Windows/i', $ua))             return 'Windows PC';
        if (preg_match('/Linux/i', $ua))               return 'Linux';
        return 'Unknown';
    }

    public function getDeviceIconAttribute(): string
    {
        return match($this->device_type) {
            'Tablet', 'Mobile' => '📱',
            'Mac'              => '💻',
            'Windows PC'       => '🖥️',
            'Linux'            => '🖥️',
            default            => '🌐',
        };
    }

    public function getBrowserAttribute(): string
    {
        $ua = $this->user_agent ?? '';
        if (preg_match('/Edg/i', $ua))     return 'Edge';
        if (preg_match('/Firefox/i', $ua)) return 'Firefox';
        if (preg_match('/Chrome/i', $ua))  return 'Chrome';
        if (preg_match('/Safari/i', $ua))  return 'Safari';
        return 'Unknown';
    }
}
