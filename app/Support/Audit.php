<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class Audit
{
    /**
     * Production-friendly audit logging for staff actions.
     */
    public static function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        array $meta = [],
        string $severity = 'info',
        ?Request $request = null
    ): void {
        try {
            $request ??= request();

            $user = auth('reception')->user();

            AuditLog::create([
                'actor_id'    => $user?->id,
                'actor_name'  => $user?->name,
                'actor_email' => $user?->email,
                'actor_role'  => $user?->role,

                'action'      => $action,
                'severity'    => in_array($severity, ['info','warning','danger'], true) ? $severity : 'info',

                'entity_type' => $entityType,
                'entity_id'   => $entityId,

                'route'       => optional($request->route())->getName(),
                'url'         => substr((string)$request->fullUrl(), 0, 255),
                'method'      => $request->method(),
                'ip'          => $request->ip(),
                'user_agent'  => substr((string)$request->userAgent(), 0, 255),

                'meta'        => $meta ?: null,
            ]);
        } catch (\Throwable $e) {
            // Never break the app because logging failed.
            // Intentionally swallowed in production.
        }
    }
}
