<?php

namespace App\Services;

use App\Models\SecurityLog;
use Illuminate\Http\Request;

class SecurityAuditService
{
    public function log(string $event, ?int $userId = null, ?array $context = null, ?Request $request = null): void
    {
        SecurityLog::create([
            'user_id' => $userId,
            'event' => $event,
            'context' => $context,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
