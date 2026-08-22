<?php

declare(strict_types=1);

namespace App\Domain\Tenancy;

use App\Models\Tenant;
use LogicException;

final class TenantContext
{
    private ?Tenant $tenant = null;

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }

    public function has(): bool
    {
        return $this->tenant !== null;
    }

    public function tenant(): Tenant
    {
        return $this->tenant ?? throw new LogicException('Tenant context has not been resolved.');
    }

    public function id(): int
    {
        return $this->tenant()->getKey();
    }
}
