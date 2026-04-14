<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommitteeAsset extends Model
{
    use SoftDeletes;

    protected $table = 'committee_assets';
    protected $fillable = [
        'asset_type', 'description', 'serial_number', 'quantity',
        'serviceable_qty', 'last_test_date', 'power_runtime_hours',
        'location', 'owner', 'notes', 'created_by',
    ];
    protected $casts = ['last_test_date' => 'date'];

    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function serviceabilityPct(): int
    {
        return $this->quantity > 0
            ? (int) round(($this->serviceable_qty / $this->quantity) * 100)
            : 0;
    }

    public function isTestOverdue(): bool
    {
        return !$this->last_test_date || $this->last_test_date->lessThan(now()->subMonths(6));
    }
}
