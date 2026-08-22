<?php

declare(strict_types=1);

namespace App\Application\Tenancy\Actions;

use App\Application\Tenancy\Data\CreateTenantData;
use App\Domain\Identity\Enums\UserStatus;
use App\Domain\Tenancy\Enums\TenantStatus;
use App\Models\Module;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CreateTenantAction
{
    public function execute(CreateTenantData $data): Tenant
    {
        return DB::transaction(function () use ($data): Tenant {
            $tenant = Tenant::query()->create([
                'public_id' => (string) Str::uuid(),
                'slug' => $data->slug,
                'status' => TenantStatus::Active,
                'default_locale' => $data->locale,
                'timezone' => $data->timezone,
            ]);

            Organization::withoutGlobalScopes()->create([
                'tenant_id' => $tenant->id,
                'public_id' => (string) Str::uuid(),
                'legal_name' => $data->legalName,
                'display_name' => $data->displayName,
                'email' => $data->organizationEmail,
            ]);

            $ownerRole = Role::withoutGlobalScopes()->create([
                'tenant_id' => $tenant->id,
                'public_id' => (string) Str::uuid(),
                'name' => 'Company Owner',
                'code' => 'company_owner',
                'is_system' => true,
            ]);

            $ownerRole->permissions()->sync(Permission::query()->pluck('id'));

            $owner = User::withoutGlobalScopes()->create([
                'tenant_id' => $tenant->id,
                'public_id' => (string) Str::uuid(),
                'name' => $data->ownerName,
                'email' => mb_strtolower($data->ownerEmail),
                'password' => $data->ownerPassword,
                'status' => UserStatus::Active,
                'is_platform_admin' => false,
            ]);

            $owner->roles()->attach($ownerRole->id);

            Module::query()->where('is_core', true)->each(function (Module $module) use ($tenant): void {
                TenantModule::withoutGlobalScopes()->create([
                    'tenant_id' => $tenant->id,
                    'module_id' => $module->id,
                    'enabled' => true,
                    'configuration' => [],
                ]);
            });

            return $tenant->load('organization');
        });
    }
}
