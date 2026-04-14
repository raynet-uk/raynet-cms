<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertStatus extends Model
{
    /**
     * Database table.
     */
    protected $table = 'alert_statuses';

    /**
     * We only store these three fields.
     */
    protected $fillable = [
        'level',
        'headline',
        'message',
    ];

    /**
     * No created_at / updated_at columns needed.
     */
    public $timestamps = false;

    /**
     * Static config for all 5 levels.
     *
     * 1 – ACTIVE INCIDENT (Red)
     * 2 – Incident IMMINENT (Orange)
     * 3 – Incident PROBABLE (Yellow)
     * 4 – Incident POSSIBLE / Training Exercise (Purple)
     * 5 – No Incidents (Green)
     */
    public static function config(): array
    {
        return [
            1 => [
                'title'       => 'Alert Level 1 – ACTIVE INCIDENT',
                'description' => 'RAYNET is fully activated and providing live emergency communications in support of responder agencies. Operators are deployed, nets are running, and traffic is being passed.',
                'colour'      => '#ef4444', // red
            ],
            2 => [
                'title'       => 'Alert Level 2 – Incident IMMINENT',
                'description' => 'A serious incident is expected or developing. RAYNET is on heightened readiness and operators should be prepared to mobilise at short notice.',
                'colour'      => '#f97316', // orange
            ],
            3 => [
                'title'       => 'Alert Level 3 – Incident PROBABLE',
                'description' => 'There is a strong likelihood of a RAYNET activation (e.g. severe weather, major events). Operators should ensure batteries are charged and go-bags ready.',
                'colour'      => '#facc15', // yellow
            ],
            4 => [
                'title'       => 'Alert Level 4 – Incident POSSIBLE / Training Exercise',
                'description' => 'Conditions exist where RAYNET could be called upon, or an exercise is in progress. Members should maintain general readiness and treat exercises as real.',
                'colour'      => '#a855f7', // purple
            ],
            5 => [
                'title'       => 'Alert Level 5 – No Incidents',
                'description' => 'No known threats or incidents. Routine monitoring and training only; operators maintain normal preparedness.',
                'colour'      => '#22c55e', // green
            ],
        ];
    }

    /**
     * Convenience: metadata for this specific record’s level.
     */
    public function meta(): array
    {
        $config = static::config();
        $level  = $this->level ?? 5;

        return $config[$level] ?? $config[5];
    }

    /**
     * Optional helper if we ever want to call AlertStatus::current().
     */
    public static function current(): ?self
    {
        return static::query()->first();
    }
}