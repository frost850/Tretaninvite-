<?php

namespace App\Services;

use App\Models\AdminAuditLog;

/**
 * Lightweight audit logger for admin actions.
 * Logs are append-only — never modify or delete them.
 * All calls are wrapped in try/catch so a logging failure never breaks the application.
 */
class AuditLogger
{
    public static function log(
        string $action,
        ?string $targetType = null,
        string|int|null $targetId = null,
        array $details = [],
        ?\Illuminate\Http\Request $request = null
    ): void {
        try {
            $req = $request ?? request();

            AdminAuditLog::create([
                'actor_email' => session('admin_email', 'unknown'),
                'actor_type'  => session('admin_is_super', false) ? 'super_admin' : 'sub_admin',
                'action'      => $action,
                'target_type' => $targetType,
                'target_id'   => $targetId !== null ? (string) $targetId : null,
                'details'     => empty($details) ? null : $details,
                'ip_address'  => $req->ip(),
                'user_agent'  => $req->userAgent() ? mb_substr($req->userAgent(), 0, 512) : null,
                'created_at'  => now(),
            ]);
        } catch (\Throwable) {
            // Audit logging must NEVER break the main application flow
        }
    }
}
