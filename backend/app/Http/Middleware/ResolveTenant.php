<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Tenancy\Enums\TenantStatus;
use App\Domain\Tenancy\TenantContext;
use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ResolveTenant
{
    public function __construct(private readonly TenantContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $tenant = Tenant::query()->findOrFail($user->tenant_id);

        if ($tenant->status !== TenantStatus::Active) {
            abort(403, 'Tenant is not active.');
        }

        // The tenant boundary comes from the authenticated identity, never from a client header.
        $this->context->set($tenant);

        try {
            return $next($request);
        } finally {
            $this->context->clear();
        }
    }
}
