<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MemberAvailability extends Model
{
    protected $fillable = ['user_id', 'from_date', 'to_date', 'reason'];

    protected $casts = [
        'from_date' => 'date',
        'to_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function daysCount(): int
    {
        return $this->from_date->diffInDays($this->to_date) + 1;
    }

    public function getDateRangeAttribute(): string
    {
        if ($this->from_date->isSameDay($this->to_date)) {
            return $this->from_date->format('d M Y');
        }
        if ($this->from_date->month === $this->to_date->month) {
            return $this->from_date->format('d') . '–' . $this->to_date->format('d M Y');
        }
        return $this->from_date->format('d M') . ' – ' . $this->to_date->format('d M Y');
    }
}