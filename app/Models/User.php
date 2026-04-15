<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable implements MustVerifyEmail
{

    public function memberRoles()
    {
        return $this->belongsToMany(\App\Models\MemberRole::class, 'member_role_user');
    }
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // ── Custom notifications ───────────────────────────────────────────────

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // ── Role helpers ───────────────────────────────────────────────────────
    // Thin wrappers around Spatie so all existing call-sites keep working.

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super-admin']);
    }

    public function isCommittee(): bool
    {
        // Committee access is granted to committee, admin, and super-admin
        return $this->hasRole(['committee', 'admin', 'super-admin']);
    }

    /**
     * Whether this user can be managed by the given manager.
     * Super-admins can only be managed by other super-admins.
     */
    public function canBeManagedBy(User $manager): bool
    {
        if ($this->isSuperAdmin() && !$manager->isSuperAdmin()) {
            return false;
        }
        return true;
    }

    // ── Backwards-compat accessors ─────────────────────────────────────────
    // Blade/controllers that still use $user->is_admin or $user->is_super_admin
    // will continue to work without any changes.

    public function getIsAdminAttribute(): bool
    {
        return $this->isAdmin();
    }

    public function getIsSuperAdminAttribute(): bool
    {
        return $this->isSuperAdmin();
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function availability(): HasOne
    {
        return $this->hasOne(CommitteeAvailability::class);
    }

    // ── Mass assignment ────────────────────────────────────────────────────

    protected $fillable = [
        'name', 'email',
        'email_verified_at', 'password',
        'callsign', 'pending_callsign',
        'dmr_id',
        'licence_class', 'licence_number',
        'echolink_number', 'dstar_callsign', 'c4fm_callsign', 'aprs_ssid',
        'modes',
        'available_for_callout', 'has_vehicle', 'vehicle_type', 'max_travel_miles',
        'nok_name', 'nok_relationship', 'nok_phone',
        'registration_pending',
        'force_password_reset', 'password_changed_at',
        'operator_title',   // ← renamed from 'role' — RAYNET operator title
        'level', 'status', 'phone', 'joined_at', 'notes',
        'admin_message', 'dismissed_broadcast_id',
        'attended_event_this_year', 'events_attended_this_year',
        'volunteering_hours_this_year',
        // Legacy columns kept in DB for now, removed after migration verified:
        'is_admin', 'is_super_admin',
        'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at'            => 'datetime',
        'password'                     => 'hashed',
        'password_changed_at'          => 'datetime',
        'force_password_reset'         => 'boolean',
        'is_admin'                     => 'boolean',
        'is_super_admin'               => 'boolean',
        'registration_pending'         => 'boolean',
        'joined_at'                    => 'date',
        'level'                        => 'integer',
        'completed_course_ids'         => 'array',
        'modes'                        => 'array',
        'available_for_callout'        => 'boolean',
        'has_vehicle'                  => 'boolean',
        'max_travel_miles'             => 'integer',
        'attended_event_this_year'     => 'boolean',
        'events_attended_this_year'    => 'integer',
        'volunteering_hours_this_year' => 'decimal:1',
    ];
}