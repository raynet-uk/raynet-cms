<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Operator extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Fields that can be mass-assigned.
     */
    protected $fillable = [
        'name',
        'email',
        'callsign',
        'role',
        'level',
        'status',
        'joined_at',
        'notes',
        'password',
        'is_admin',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'joined_at' => 'date',
        'is_admin'  => 'boolean',
    ];

    /**
     * Hide sensitive fields when serialised.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Helper for the combined “role / level” label on the badge.
     */
    public function displayLevel(): string
    {
        if ($this->role && $this->level) {
            return $this->role . ' / ' . $this->level;
        }

        if ($this->role) {
            return $this->role;
        }

        return $this->level ?? '';
    }

    /**
     * For Laravel Auth: we still use the primary key as the identifier.
     * (Login will accept email OR callsign in the controller logic.)
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }
}