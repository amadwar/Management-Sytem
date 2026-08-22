<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Domain\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            $context = app(TenantContext::class);

            if ($context->has()) {
                $builder->where($builder->qualifyColumn('tenant_id'), $context->id());
            }
        });

        static::creating(function (Model $model): void {
            $context = app(TenantContext::class);

            // Tenant-owned records receive tenant_id centrally so callers cannot accidentally
            // create unscoped data by forgetting the foreign key in a controller/action.
            if ($context->has() && empty($model->getAttribute('tenant_id'))) {
                $model->setAttribute('tenant_id', $context->id());
            }
        });
    }
}
