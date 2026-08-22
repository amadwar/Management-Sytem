<?php

declare(strict_types=1);

namespace App\Application\Tenancy\Data;

final readonly class CreateTenantData
{
    public function __construct(
        public string $slug,
        public string $legalName,
        public string $displayName,
        public string $organizationEmail,
        public string $ownerName,
        public string $ownerEmail,
        public string $ownerPassword,
        public string $timezone = 'UTC',
        public string $locale = 'en',
    ) {}
}
