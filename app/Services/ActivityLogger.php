<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(
        string $action,
        string $module,
        ?string $description = null,
        ?int $recordId = null
    ): void {
        ActivityLog::create([
            'account_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'record_id' => $recordId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
