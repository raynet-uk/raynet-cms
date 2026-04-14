<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventDocument extends Model
{
    protected $fillable = [
        'event_id',
        'filename',
        'label',
        'disk',
        'path',
        'size_bytes',
        'sort_order',
        'uploaded_by',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'sort_order' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}