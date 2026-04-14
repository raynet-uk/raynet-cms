<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeAvailability extends Model
{
    protected $table = 'committee_availability';
    protected $fillable = [
        'user_id', 'is_active_operator', 'deployable_60min', 'deployable_120min',
        'is_team_leader', 'induction_current', 'message_handling_current',
        'digital_data_competent', 'induction_date', 'message_handling_date',
        'notes', 'updated_by',
    ];
    protected $casts = [
        'is_active_operator'       => 'boolean',
        'deployable_60min'         => 'boolean',
        'deployable_120min'        => 'boolean',
        'is_team_leader'           => 'boolean',
        'induction_current'        => 'boolean',
        'message_handling_current' => 'boolean',
        'digital_data_competent'   => 'boolean',
        'induction_date'           => 'date',
        'message_handling_date'    => 'date',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function updatedBy(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
}
