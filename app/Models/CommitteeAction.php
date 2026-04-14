<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommitteeAction extends Model
{
    use SoftDeletes;

    protected $table = 'committee_actions';
    protected $fillable = [
        'title', 'description', 'source', 'source_ref', 'owner_id',
        'due_date', 'priority', 'status', 'closed_date', 'closure_notes', 'created_by',
    ];
    protected $casts = [
        'due_date'    => 'date',
        'closed_date' => 'date',
    ];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast()
            && !in_array($this->status, ['closed', 'cancelled']);
    }

    public function priorityColour(): string
    {
        return match($this->priority) {
            'critical' => 'red', 'high' => 'orange', 'medium' => 'amber', default => 'grey',
        };
    }
}
