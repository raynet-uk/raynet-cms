<?php

namespace App\Helpers;

use App\Models\AdminAuditLog;
use Illuminate\Support\Str;

class AuditLogger
{
    /**
     * Record an admin action.
     * Writes to both admin_audit_logs (legacy super admin panel)
     * and audit_log (yammi dashboard).
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
        // ── 1. Legacy table (keeps super admin panel working) ─────────────
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
            \Illuminate\Support\Facades\Log::warning('AuditLogger (legacy) failed: ' . $e->getMessage());
        }

        // ── 2. Yammi audit_log table ──────────────────────────────────────
        try {
            $user       = auth()->user();
            $actorId    = $user?->id ? (string) $user->id : null;
            $actorLabel = $user?->name ?? 'System';

            $auditable_type = $entity ? get_class($entity) : 'App\Models\AdminAction';
            $auditable_id   = (string) ($entity?->id ?? 0);

            \Illuminate\Support\Facades\DB::table('audit_log')->insert([
                'auditable_type' => $auditable_type,
                'auditable_id'   => $auditable_id,
                'event'          => 'updated',
                'changes'        => json_encode([
                    'old' => $old ?: [],
                    'new' => $new ?: [],
                ]),
                'actor_type'     => 'user',
                'actor_id'       => $actorId,
                'actor_label'    => $actorLabel,
                'origin_type'    => 'user',
                'origin_id'      => $actorId,
                'origin_label'   => $actorLabel,
                'labels'         => json_encode([
                    'action'       => $action,
                    'description'  => $description,
                    'entity_label' => $entity?->name ?? $entity?->title ?? $entity?->email ?? null,
                    'ip_address'   => request()->ip(),
                ]),
                'correlation_id' => Str::uuid()->toString(),
                'is_noise'       => 0,
                'occurred_at'    => now(),
                'created_at'     => now(),
                'context'        => json_encode([
                    'source'      => 'AuditLogger',
                    'action'      => $action,
                    'description' => $description,
                    'ip_address'  => request()->ip(),
                    'entity_type' => $entity ? class_basename($entity) : null,
                    'entity_id'   => $entity?->id ?? null,
                ]),
                'integrity_hash' => null,
                'chain_depth'    => 0,
                'tenant_id'      => null,
                'reason'         => $description,
                'event_version'  => 1,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('AuditLogger (yammi) failed: ' . $e->getMessage());
        }
    }
}
