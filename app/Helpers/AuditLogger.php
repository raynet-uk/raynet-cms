<?php

namespace App\Helpers;

use App\Models\AdminAuditLog;

class AuditLogger
{
    /**
     * Record an admin action.
     *
     * Usage examples:
     *   AuditLogger::log('user.suspended', $user, "Suspended {$user->name}");
     *   AuditLogger::log('user.deleted', $user, "Deleted {$user->name}", ['status' => 'Active'], []);
     *   AuditLogger::log('maintenance.enabled', null, "Maintenance mode enabled");
     */
    public static function log(
        string $action,
        mixed  $entity = null,
        string $description = '',
        array  $old = [],
        array  $new = []
    ): void {
        try {
            AdminAuditLog::create([
                'admin_id'     => auth()->id(),
                'action'       => $action,
                'entity_type'  => $entity ? class_basename($entity) : null,
                'entity_id'    => $entity?->id ?? null,
                'entity_label' => $entity?->name ?? $entity?->title ?? $entity?->email ?? null,
                'description'  => $description,
                'old_values'   => $old ?: null,
                'new_values'   => $new ?: null,
                'ip_address'   => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            // Fail silently — audit logging should never break the app
            \Illuminate\Support\Facades\Log::warning('AuditLogger failed: ' . $e->getMessage());
        }
    }
}
