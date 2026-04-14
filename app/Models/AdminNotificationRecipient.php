<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotificationRecipient extends Model
{
    protected $fillable = [
        'notification_id', 'user_id', 'read_at', 'removed_at',
        'email_token', 'email_opened_at',
    ];

    protected $casts = [
        'read_at'         => 'datetime',
        'removed_at'      => 'datetime',
        'email_opened_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(AdminNotification::class, 'notification_id');
    }
}