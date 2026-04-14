<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadinessScore extends Model
{
    protected $fillable = [
        'indicator_id', 'raw_score', 'evidence_ref',
        'evidence_date', 'notes', 'scored_by',
    ];

    protected $casts = [
        'evidence_date' => 'date',
        'raw_score' => 'integer',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(ReadinessIndicator::class, 'indicator_id');
    }

    public function scoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scored_by');
    }

    /**
     * Is the evidence current (within 12 months)?
     */
    public function isEvidenceCurrent(): bool
    {
        return $this->evidence_date && $this->evidence_date->greaterThan(now()->subYear());
    }
}
