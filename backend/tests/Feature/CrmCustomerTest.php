<?php

use App\Domain\Identity\Enums\UserStatus;
use App\Models\CrmCustomer;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Models\User;
use Illuminate\Support\Str;

function crmUser(Tenant $tenant): User
{
    $codes = ['crm.customers.view', 'crm.customers.create', 'crm.customers.update', 'crm.customers.delete', 'crm.notes.create', 'crm.activities.create', 'crm.leads.view', 'crm.leads.create', 'crm.leads.update', 'crm.leads.convert'];
    foreach ($codes as $code) {
        Permission::query()->firstOrCreate(['code' => $code], ['name' => $code, 'module_code' => 'crm']);
    }
    $role = Role::withoutGlobalScopes()->create(['tenant_id' => $tenant->id, 'public_id' => (string) Str::uuid(), 'name' => 'CRM Manager', 'code' => 'crm_manager', 'is_system' => false]);
    $role->permissions()->sync(Permission::query()->whereIn('code', $codes)->pluck('id'));
    $user = User::factory()->create(['tenant_id' => $tenant->id, 'status' => UserStatus::Active]);
    $user->roles()->attach($role->id);
    $module = Module::query()->firstOrCreate(['code' => 'crm'], ['name' => 'CRM', 'description' => 'CRM', 'is_core' => false, 'is_active' => true]);
    TenantModule::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'module_id' => $module->id], ['enabled' => true, 'configuration' => []]);

    return $user;
}

it('creates and lists a tenant customer', function () {
    $tenant = Tenant::factory()->create();
    $user = crmUser($tenant);
    $token = $user->createToken('test')->plainTextToken;
    $this->withToken($token)->postJson('/api/v1/crm/customers', ['type' => 'company', 'status' => 'active', 'name' => 'Acme GmbH', 'email' => 'hello@acme.test'])->assertSuccessful()->assertJsonPath('data.name', 'Acme GmbH');
    $this->withToken($token)->getJson('/api/v1/crm/customers')->assertOk()->assertJsonFragment(['name' => 'Acme GmbH']);
});

it('does not expose another tenant crm customer', function () {
    $a = Tenant::factory()->create();
    $b = Tenant::factory()->create();
    $user = crmUser($a);
    $token = $user->createToken('test')->plainTextToken;
    $foreign = CrmCustomer::withoutGlobalScopes()->create(['tenant_id' => $b->id, 'public_id' => (string) Str::uuid(), 'type' => 'company', 'status' => 'active', 'name' => 'Foreign']);
    $this->withToken($token)->getJson('/api/v1/crm/customers/'.$foreign->public_id)->assertNotFound();
    $this->withToken($token)->getJson('/api/v1/crm/customers')->assertJsonMissing(['name' => 'Foreign']);
});

it('requires crm module activation', function () {
    $tenant = Tenant::factory()->create();
    $user = crmUser($tenant);
    $token = $user->createToken('test')->plainTextToken;
    TenantModule::withoutGlobalScopes()->where('tenant_id', $tenant->id)->update(['enabled' => false]);
    $this->withToken($token)->getJson('/api/v1/crm/customers')->assertForbidden();
});
