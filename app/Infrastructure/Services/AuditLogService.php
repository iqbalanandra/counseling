<?php

namespace App\Infrastructure\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    public static function log(
        string $action,
        string $module,
        string $description,
        ?string $resourceId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        try {
            $user = Auth::user();

            AuditLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? 'System',
                'action' => $action,
                'module' => $module,
                'resource_id' => $resourceId,
                'description' => $description,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Fail gracefully to not disrupt transaction
        }
    }
}
