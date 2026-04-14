<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommitteeExercise extends Model
{
    use SoftDeletes;

    protected $table = 'committee_exercises';
    protected $fillable = [
        'date', 'activity', 'type', 'capability_tested', 'lead',
        'outcome', 'lessons_identified', 'action_owner',
        'due_date', 'closed_date', 'notes', 'created_by',
    ];
    protected $casts = [
        'date'        => 'date',
        'due_date'    => 'date',
        'closed_date' => 'date',
    ];

    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function typeLabel(): string
    {
        return match($this->type) {
            'training_night'     => 'Training night',
            'tabletop'           => 'Tabletop exercise',
            'practical_exercise' => 'Practical exercise',
            'real_deployment'    => 'Real deployment',
            'partner_exercise'   => 'Partner exercise',
            default              => 'Other',
        };
    }
}
