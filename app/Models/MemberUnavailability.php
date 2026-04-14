<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberUnavailability extends Model
{
    protected $table = 'member_unavailability';

    protected $fillable = ['user_id', 'from_date', 'to_date', 'reason'];

    protected $casts = [
        'from_date' => 'date',
        'to_date'   => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    /** Periods that overlap a given date range. */
    public function scopeOverlapping($query, Carbon $from, Carbon $to)
    {
        return $query->where('from_date', '<=', $to)
                     ->where('to_date',   '>=', $from);
    }

    /** Currently active unavailability periods. */
    public function scopeCurrent($query)
    {
        return $query->where('to_date', '>=', now()->toDateString());
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getDateRangeAttribute(): string
    {
        if ($this->from_date->isSameDay($this->to_date)) {
            return $this->from_date->format('D j M Y');
        }
        if ($this->from_date->isSameMonth($this->to_date)) {
            return $this->from_date->format('j') . '–' . $this->to_date->format('j M Y');
        }
        return $this->from_date->format('j M') . ' – ' . $this->to_date->format('j M Y');
    }

    public function daysCount(): int
    {
        return $this->from_date->diffInDays($this->to_date) + 1;
    }

    /**
     * Check whether a given user is unavailable on a specific date.
     */
    public static function isUserUnavailableOn(int $userId, Carbon $date): bool
    {
        return static::where('user_id', $userId)
            ->where('from_date', '<=', $date)
            ->where('to_date', '>=', $date)
            ->exists();
    }
}
