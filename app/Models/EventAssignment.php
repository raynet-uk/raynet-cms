<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class EventAssignment extends Model
{
    protected $fillable = [
        'event_id', 'user_id',
        'role', 'callsign',
        'frequency', 'mode', 'ctcss_tone', 'channel_label',
        'secondary_frequency', 'secondary_mode', 'secondary_ctcss',
        'fallback_frequency', 'fallback_mode', 'fallback_ctcss',
        'location_name', 'lat', 'lng', 'grid_ref', 'what3words', 'coverage_radius_m',
        'report_time', 'start_time', 'end_time', 'depart_time', 'shifts',
        'equipment', 'equipment_items',
        'briefing_notes', 'medical_notes',
        'emergency_contact_name', 'emergency_contact_phone',
        'has_vehicle', 'vehicle_reg', 'first_aid_trained',
        'status', 'status_changed_at', 'status_note',
        'briefing_sent', 'briefing_sent_at',
        'briefing_token', 'attendance_status', 'attendance_log',
    ];

    protected $casts = [
        'shifts'            => 'array',
        'equipment_items'   => 'array',
        'attendance_log'    => 'array',
        'has_vehicle'       => 'boolean',
        'first_aid_trained' => 'boolean',
        'briefing_sent'     => 'boolean',
        'lat'               => 'float',
        'lng'               => 'float',
        'coverage_radius_m' => 'integer',
        'status_changed_at' => 'datetime',
        'briefing_sent_at'  => 'datetime',
    ];

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (EventAssignment $a) {
            if (empty($a->briefing_token)) {
                $a->briefing_token = Str::random(48);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Status helpers ────────────────────────────────────────────────────────

    public function statusLabel(): string
    {
        return match ($this->status) {
            'confirmed' => 'Confirmed',
            'standby'   => 'Standby',
            'declined'  => 'Declined',
            default     => 'Pending',
        };
    }

    public function statusColour(): string
    {
        return match ($this->status) {
            'confirmed' => 'green',
            'standby'   => 'amber',
            'declined'  => 'red',
            default     => 'navy',
        };
    }

    public function markerColour(): string
    {
        return match ($this->status) {
            'confirmed' => '#1a6b3c',
            'standby'   => '#8a5c00',
            'declined'  => '#C8102E',
            default     => '#003366',
        };
    }

    // ── Attendance helpers ────────────────────────────────────────────────────

    public function attendanceLabel(): string
    {
        return match ($this->attendance_status) {
            'checked_in'  => 'Checked In',
            'on_break'    => 'On Break',
            'checked_out' => 'Checked Out',
            default       => 'Not Arrived',
        };
    }

    public function attendanceColour(): string
    {
        return match ($this->attendance_status) {
            'checked_in'  => 'green',
            'on_break'    => 'amber',
            'checked_out' => 'navy',
            default       => 'grey',
        };
    }

    public function briefingUrl(): string
    {
        if (empty($this->briefing_token)) {
            return '#'; // token not yet generated — run the attendance migration
        }

        return route('operator.brief', ['token' => $this->briefing_token]);
    }

    public function canCheckIn(): bool
    {
        if ($this->attendance_status !== 'not_arrived') {
            return false;
        }

        // Only allow check-in on the event's start date (in the event's timezone,
        // or London time as the fallback for RAYNET UK deployments).
        $eventDate = $this->event?->starts_at;
        if ($eventDate) {
            $today = now()->timezone('Europe/London')->toDateString();
            $eDate = $eventDate->timezone('Europe/London')->toDateString();
            if ($today !== $eDate) {
                return false;
            }
        }

        return true;
    }

    public function canStartBreak(): bool
    {
        return $this->attendance_status === 'checked_in';
    }

    public function canEndBreak(): bool
    {
        return $this->attendance_status === 'on_break';
    }

    public function canCheckOut(): bool
    {
        return in_array($this->attendance_status, ['checked_in', 'on_break'], true);
    }

    /**
     * Append to attendance log and sync the status column.
     */
    public function recordAttendance(string $type, ?string $note = null): void
    {
        $log   = $this->attendance_log ?? [];
        $log[] = [
            'type' => $type,
            'time' => now()->toIso8601String(),
            'note' => $note ?? '',
        ];

        $this->update([
            'attendance_log'    => $log,
            'attendance_status' => match ($type) {
                'check_in'    => 'checked_in',
                'break_start' => 'on_break',
                'break_end'   => 'checked_in',
                'check_out'   => 'checked_out',
                default       => $this->attendance_status,
            },
        ]);
    }

    /**
     * Total minutes spent on break across all break pairs in the log.
     */
    public function totalBreakMinutes(): int
    {
        $log   = $this->attendance_log ?? [];
        $total = 0;
        $start = null;

        foreach ($log as $entry) {
            if ($entry['type'] === 'break_start') {
                $start = $entry['time'];
            } elseif ($entry['type'] === 'break_end' && $start) {
                $total += (int) round((strtotime($entry['time']) - strtotime($start)) / 60);
                $start  = null;
            }
        }

        return $total;
    }

    /**
     * Net duty minutes (total elapsed minus breaks), up to check-out or now.
     */
    public function dutyMinutes(): ?int
    {
        $log         = $this->attendance_log ?? [];
        $checkIn     = null;
        $checkOut    = null;

        foreach ($log as $entry) {
            if ($entry['type'] === 'check_in' && !$checkIn) {
                $checkIn = $entry['time'];
            }
            if ($entry['type'] === 'check_out') {
                $checkOut = $entry['time'];
            }
        }

        if (!$checkIn) {
            return null;
        }

        $endTs = $checkOut ? strtotime($checkOut) : time();
        return max(0, (int) round(($endTs - strtotime($checkIn)) / 60) - $this->totalBreakMinutes());
    }

    // ── Schedule helpers ──────────────────────────────────────────────────────

    public function shiftWindow(): ?string
    {
        $shifts = $this->shifts;

        if (!empty($shifts)) {
            foreach ($shifts as $s) {
                if (($s['type'] ?? 'shift') === 'shift' && !empty($s['start'])) {
                    $start = substr($s['start'], 0, 5);
                    $end   = !empty($s['end']) ? substr($s['end'], 0, 5) : null;
                    return $end ? "{$start} – {$end}" : $start;
                }
            }
        }

        if ($this->start_time) {
            $start = substr($this->start_time, 0, 5);
            $end   = $this->end_time ? substr($this->end_time, 0, 5) : null;
            return $end ? "{$start} – {$end}" : $start;
        }

        return null;
    }

    public function totalHours(): ?float
    {
        $shifts  = $this->shifts;
        $minutes = 0;

        if (!empty($shifts)) {
            foreach ($shifts as $s) {
                if (($s['type'] ?? 'shift') !== 'shift' || empty($s['start']) || empty($s['end'])) {
                    continue;
                }
                [$sh, $sm] = array_map('intval', explode(':', $s['start']));
                [$eh, $em] = array_map('intval', explode(':', $s['end']));
                $diff = ($eh * 60 + $em) - ($sh * 60 + $sm);
                if ($diff > 0) {
                    $minutes += $diff;
                }
            }
        }

        return $minutes > 0 ? round($minutes / 60, 1) : null;
    }
}