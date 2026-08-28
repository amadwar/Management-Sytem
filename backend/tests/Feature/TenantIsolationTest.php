<?php

use App\Domain\Identity\Enums\UserStatus;
use App\Models\Branch;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

function grantBranchView(User $user, Tenant $tenant): void
{
    $permission = Permission::query()->firstOrCreate(['code' => 'branches.view'], ['name' => 'View Branches', 'module_code' => 'branches']);
    $role = Role::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'public_id' => (string) Str::uuid(), 'name' => 'Branch Viewer', 'code' => 'branch_viewer', 'is_system' => false]);
    $role->permissions()->attach($permission->id);
    $user->roles()->attach($role->id);
}

it('does not expose another tenant branch', function () {
    $a = Tenant::factory()->create();
    $b = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $a->id, 'status' => UserStatus::Active]);
    grantBranchView($user, $a);
    $foreign = Branch::withoutGlobalScopes()->create(['tenant_id' => $b->id, 'public_id' => (string) Str::uuid(), 'name' => 'Foreign', 'is_active' => true]);
    $token = $user->createToken('test')->plainTextToken;
    $this->withToken($token)->getJson('/api/v1/branches/'.$foreign->public_id)->assertNotFound();
});

it('ignores a forged tenant header and uses the authenticated tenant', function () {
    $a = Tenant::factory()->create();
    $b = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $a->id, 'status' => UserStatus::Active]);
    grantBranchView($user, $a);
    Branch::withoutGlobalScopes()->create(['tenant_id' => $a->id, 'public_id' => (string) Str::uuid(), 'name' => 'Own', 'is_active' => true]);
    Branch::withoutGlobalScopes()->create(['tenant_id' => $b->id, 'public_id' => (string) Str::uuid(), 'name' => 'Foreign', 'is_active' => true]);
    $token = $user->createToken('test')->plainTextToken;
    $this->withToken($token)->withHeader('X-Tenant-ID', (string) $b->public_id)->getJson('/api/v1/branches')->assertOk()->assertJsonFragment(['name' => 'Own'])->assertJsonMissing(['name' => 'Foreign']);
});
