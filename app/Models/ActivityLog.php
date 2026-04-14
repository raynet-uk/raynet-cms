<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'event_name',
        'event_date',
        'hours',
        'logged_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'hours'      => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function loggedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    /**
     * Scope to a given academic year (Sep–Aug).
     * Pass the *starting* year, e.g. 2024 for Sep 2024 – Aug 2025.
     */
    public function scopeAcademicYear($query, int $startYear)
    {
        return $query->whereBetween('event_date', [
            "{$startYear}-09-01",
            ($startYear + 1) . '-08-31',
        ]);
    }

    /**
     * Returns the academic year label for this log entry, e.g. "2024/25".
     */
    public function getAcademicYearAttribute(): string
    {
        $month = $this->event_date->month;
        $year  = $this->event_date->year;
        $start = $month >= 9 ? $year : $year - 1;
        return $start . '/' . substr($start + 1, -2);
    }
}