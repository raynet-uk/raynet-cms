<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommitteeRisk extends Model
{
    use SoftDeletes;

    protected $table = 'committee_risks';
    protected $fillable = [
        'title', 'description', 'category', 'likelihood', 'impact',
        'mitigation', 'status', 'review_date', 'owner_id', 'created_by',
    ];
    protected $casts = [
        'review_date' => 'date',
        'likelihood'  => 'integer',
        'impact'      => 'integer',
    ];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_id'); }

    public function riskScore(): int { return $this->likelihood * $this->impact; }

    public function riskColour(): string
    {
        return match(true) {
            $this->riskScore() >= 15 => 'red',
            $this->riskScore() >= 9  => 'orange',
            $this->riskScore() >= 4  => 'amber',
            default                  => 'green',
        };
    }

    public function riskLabel(): string
    {
        return match(true) {
            $this->riskScore() >= 15 => 'Critical',
            $this->riskScore() >= 9  => 'High',
            $this->riskScore() >= 4  => 'Medium',
            default                  => 'Low',
        };
    }
}
