<?php

declare(strict_types=1);

use App\Domain\Identity\Enums\UserStatus;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

function giveBranchViewPermission(User $user, Tenant $tenant): void
{
    $permission = Permission::query()->firstOrCreate(
        ['code' => 'branches.view'],
        [
            'name' => 'View Branches',
            'module_code' => 'branches',
        ]
    );

    $role = Role::withoutGlobalScopes()->create([
        'tenant_id' => $tenant->id,
        'public_id' => (string) Str::uuid(),
        'name' => 'Branch Viewer',
        'code' => 'branch_viewer',
        'is_system' => false,
    ]);

    $role->permissions()->attach($permission->id);
    $user->roles()->attach($role->id);
}

it('does not expose another tenant branch', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $user = User::factory()->create([
        'tenant_id' => $tenantA->id,
        'status' => UserStatus::Active,
    ]);

    giveBranchViewPermission($user, $tenantA);

    $foreignBranch = Branch::withoutGlobalScopes()->create([
        'tenant_id' => $tenantB->id,
        'public_id' => (string) Str::uuid(),
        'name' => 'Foreign',
        'is_active' => true,
    ]);

    $token = $user->createToken('test')->plainTextToken;

    $this
        ->withToken($token)
        ->getJson('/api/v1/branches/' . $foreignBranch->public_id)
        ->assertNotFound();
});

it('ignores a forged tenant header and uses the authenticated tenant', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $user = User::factory()->create([
        'tenant_id' => $tenantA->id,
        'status' => UserStatus::Active,
    ]);

    giveBranchViewPermission($user, $tenantA);

    Branch::withoutGlobalScopes()->create([
        'tenant_id' => $tenantA->id,
        'public_id' => (string) Str::uuid(),
        'name' => 'Own',
        'is_active' => true,
    ]);

    Branch::withoutGlobalScopes()->create([
        'tenant_id' => $tenantB->id,
        'public_id' => (string) Str::uuid(),
        'name' => 'Foreign',
        'is_active' => true,
    ]);

    $token = $user->createToken('test')->plainTextToken;

    $this
        ->withToken($token)
        // This header is intentionally forged. Tenant resolution must still
        // use the authenticated user's tenant and never trust client input.
        ->withHeader('X-Tenant-ID', (string) $tenantB->public_id)
        ->getJson('/api/v1/branches')
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Own',
        ])
        ->assertJsonMissing([
            'name' => 'Foreign',
        ]);
});