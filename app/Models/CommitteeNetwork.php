<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommitteeNetwork extends Model
{
    use SoftDeletes;

    protected $table = 'committee_networks';
    protected $fillable = [
        'name', 'type', 'description', 'status', 'last_tested', 'test_result',
        'frequency_channel', 'talkgroup_network_id', 'notes', 'owner_id', 'created_by',
    ];
    protected $casts = ['last_tested' => 'date'];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }

    public function statusColour(): string
    {
        return match($this->status) {
            'operational' => 'green', 'degraded' => 'amber', 'offline' => 'red', default => 'grey',
        };
    }
}
