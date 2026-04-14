<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    protected $table = 'equipment';

    protected $fillable = [
        'user_id', 'make', 'model', 'serial_number',
        'callsign', 'licence_class', 'equipment_type',
        'last_tested_date', 'next_test_due', 'notes', 'is_active',
    ];

    protected $casts = [
        'last_tested_date' => 'date',
        'next_test_due'    => 'date',
        'is_active'        => 'boolean',
    ];

    // ── Types ─────────────────────────────────────────────────────────────────

    public const TYPES = [
        'handheld'  => ['label' => 'Handheld',        'icon' => '📻'],
        'mobile'    => ['label' => 'Mobile/Vehicle',   'icon' => '🚗'],
        'base'      => ['label' => 'Base Station',     'icon' => '🗼'],
        'hf'        => ['label' => 'HF Transceiver',   'icon' => '📡'],
        'repeater'  => ['label' => 'Repeater',         'icon' => '🔁'],
        'digital'   => ['label' => 'Digital/Data',     'icon' => '💻'],
        'antenna'   => ['label' => 'Antenna',          'icon' => '📶'],
        'other'     => ['label' => 'Other',            'icon' => '🔧'],
    ];

    public const LICENCE_CLASSES = ['Foundation', 'Intermediate', 'Full'];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getTypeIconAttribute(): string
    {
        return self::TYPES[$this->equipment_type]['icon'] ?? '🔧';
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->equipment_type]['label'] ?? 'Other';
    }

    public function getDisplayNameAttribute(): string
    {
        return trim("{$this->make} {$this->model}");
    }

    /**
     * Is the test date overdue? (> 1 year old or past next_test_due)
     */
    public function isTestOverdue(): bool
    {
        if ($this->next_test_due) {
            return $this->next_test_due->isPast();
        }
        if ($this->last_tested_date) {
            return $this->last_tested_date->addYear()->isPast();
        }
        return false;
    }

    public function testStatusBadge(): array
    {
        if (! $this->last_tested_date) {
            return ['label' => 'Never tested', 'class' => 'badge-grey'];
        }
        if ($this->isTestOverdue()) {
            return ['label' => 'Test overdue', 'class' => 'badge-red'];
        }
        $daysUntil = now()->diffInDays($this->next_test_due ?? $this->last_tested_date->addYear(), false);
        if ($daysUntil <= 60) {
            return ['label' => 'Test due soon', 'class' => 'badge-amber'];
        }
        return ['label' => 'In test', 'class' => 'badge-green'];
    }
}
