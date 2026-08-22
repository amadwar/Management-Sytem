<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Tenancy\TenantContext;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
    }

    public function boot(): void
    {
        // Domain-specific bootstrapping belongs in dedicated providers as the platform grows.
    }
}
