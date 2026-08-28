<?php

declare(strict_types=1);

namespace App\Application\Audit;

use App\Domain\Tenancy\TenantContext;
use App\Models\AuditLog;
use Illuminate\Http\Request;

final class AuditLogger
{
    public function record(string $action, Request $request, ?string $type = null, int|string|null $id = null, array $metadata = []): void
    {
        $user = $request->user();
        $context = app(TenantContext::class);

        AuditLog::query()->create([
            'tenant_id' => $context->has() ? $context->id() : $user?->tenant_id,
            'user_id' => $user?->getAuthIdentifier(),
            'action' => $action,
            'auditable_type' => $type,
            'auditable_id' => $id,
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 1000),
            'metadata' => $metadata,
        ]);
    }
}
