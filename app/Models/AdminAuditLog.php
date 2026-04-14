<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'entity_type',
        'entity_id',
        'entity_label',
        'description',
        'old_values',
        'new_values',
        'ip_address',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'user.created'          => 'User Created',
            'user.updated'          => 'User Updated',
            'user.deleted'          => 'User Deleted',
            'user.suspended'        => 'User Suspended',
            'user.unsuspended'      => 'User Reinstated',
            'user.promoted'         => 'User Promoted',
            'user.impersonated'     => 'Impersonation',
            'user.force_logout'     => 'Force Logout',
            'user.force_pwd_reset'  => 'Force Pwd Reset',
            'user.pwd_reset_cleared'=> 'Reset Flag Cleared',
            'user.email_verified'   => 'Email Verified',
            'user.message_sent'     => 'Message Sent',
            'user.registered'       => 'Registration',
            'user.approved'         => 'Registration Approved',
            'user.rejected'         => 'Registration Rejected',
            'callsign.approved'     => 'Callsign Approved',
            'callsign.rejected'     => 'Callsign Rejected',
            'session.terminated'    => 'Session Killed',
            'session.all_terminated'=> 'All Sessions Killed',
            'maintenance.enabled'   => 'Maintenance ON',
            'maintenance.disabled'  => 'Maintenance OFF',
            'super_admin.granted'   => 'Super Admin Granted',
            'super_admin.revoked'   => 'Super Admin Revoked',
            'broadcast.sent'        => 'Broadcast Sent',
            'broadcast.cleared'     => 'Broadcast Cleared',
            default                 => ucwords(str_replace('.', ' ', $this->action)),
        };
    }

    public function getActionColourAttribute(): string
    {
        return match(true) {
            str_contains($this->action, 'deleted')          => 'red',
            str_contains($this->action, 'suspended')        => 'orange',
            str_contains($this->action, 'created')
                || str_contains($this->action, 'approved')
                || str_contains($this->action, 'granted')   => 'green',
            str_contains($this->action, 'rejected')
                || str_contains($this->action, 'revoked')   => 'red',
            str_contains($this->action, 'maintenance')      => 'purple',
            str_contains($this->action, 'impersonated')     => 'orange',
            default                                         => 'blue',
        };
    }
}
