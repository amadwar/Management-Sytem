<?php

use App\Domain\Identity\Enums\UserStatus;
use App\Models\CatalogItem;
use App\Models\Currency;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Str;

function catalogUser(Tenant $tenant): User
{
    $codes = [
        'catalog.view',
        'catalog.create',
        'catalog.update',
        'catalog.delete',
    ];

    foreach ($codes as $code) {
        Permission::query()->firstOrCreate(
            ['code' => $code],
            [
                'name' => $code,
                'module_code' => 'catalog',
            ]
        );
    }

    $role = Role::withoutGlobalScopes()->create([
        'tenant_id' => $tenant->id,
        'public_id' => (string) Str::uuid(),
        'name' => 'Catalog Manager',
        'code' => 'catalog_manager',
        'is_system' => false,
    ]);

    $role->permissions()->sync(
        Permission::query()
            ->whereIn('code', $codes)
            ->pluck('id')
    );

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'status' => UserStatus::Active,
    ]);

    $user->roles()->attach($role->id);

    return $user;
}

function catalogCurrency(): Currency
{
    return Currency::query()->firstOrCreate(
        ['code' => 'EUR'],
        [
            'name' => 'Euro',
            'symbol' => '€',
            'decimal_places' => 2,
            'is_active' => true,
        ]
    );
}

it('creates and lists a tenant catalog item', function () {
    $tenant = Tenant::factory()->create();

    $user = catalogUser($tenant);

    catalogCurrency();

    $token = $user->createToken('test')->plainTextToken;

    $response = $this
        ->withToken($token)
        ->postJson('/api/v1/catalog-items', [
            'type' => 'product',
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'description' => 'Test product description',
            'price' => 99.99,
            'currency_code' => 'EUR',
            'unit' => 'piece',
            'status' => 'active',
            'taxable' => true,
        ]);

    $response
        ->assertSuccessful()
        ->assertJsonPath('data.type', 'product')
        ->assertJsonPath('data.sku', 'PROD-001')
        ->assertJsonPath('data.name', 'Test Product')
        ->assertJsonPath('data.currency_code', 'EUR')
        ->assertJsonPath('data.status', 'active')
        ->assertJsonPath('data.taxable', true);

    $this
        ->withToken($token)
        ->getJson('/api/v1/catalog-items')
        ->assertOk()
        ->assertJsonFragment([
            'name' => 'Test Product',
        ]);
});

it('creates a service catalog item', function () {
    $tenant = Tenant::factory()->create();

    $user = catalogUser($tenant);

    catalogCurrency();

    $token = $user->createToken('test')->plainTextToken;

    $this
        ->withToken($token)
        ->postJson('/api/v1/catalog-items', [
            'type' => 'service',
            'sku' => 'SERV-001',
            'name' => 'Consulting Service',
            'description' => 'Professional consulting',
            'price' => 150,
            'currency_code' => 'EUR',
            'unit' => 'hour',
            'status' => 'active',
            'taxable' => true,
        ])
        ->assertSuccessful()
        ->assertJsonPath('data.type', 'service')
        ->assertJsonPath('data.name', 'Consulting Service');
});

it('updates a tenant catalog item', function () {
    $tenant = Tenant::factory()->create();

    $user = catalogUser($tenant);

    catalogCurrency();

    $token = $user->createToken('test')->plainTextToken;

    $item = CatalogItem::withoutGlobalScopes()->create([
        'tenant_id' => $tenant->id,
        'public_id' => (string) Str::uuid(),
        'type' => 'product',
        'sku' => 'OLD-001',
        'name' => 'Old Product',
        'description' => null,
        'price' => 10,
        'currency_code' => 'EUR',
        'unit' => 'piece',
        'status' => 'active',
        'taxable' => false,
    ]);

    $this
        ->withToken($token)
        ->putJson('/api/v1/catalog-items/'.$item->public_id, [
            'type' => 'product',
            'sku' => 'NEW-001',
            'name' => 'Updated Product',
            'description' => 'Updated description',
            'price' => 25.50,
            'currency_code' => 'EUR',
            'unit' => 'piece',
            'status' => 'active',
            'taxable' => true,
        ])
        ->assertOk()
        ->assertJsonPath('data.sku', 'NEW-001')
        ->assertJsonPath('data.name', 'Updated Product')
        ->assertJsonPath('data.taxable', true);
});

it('deletes a tenant catalog item', function () {
    $tenant = Tenant::factory()->create();

    $user = catalogUser($tenant);

    catalogCurrency();

    $token = $user->createToken('test')->plainTextToken;

    $item = CatalogItem::withoutGlobalScopes()->create([
        'tenant_id' => $tenant->id,
        'public_id' => (string) Str::uuid(),
        'type' => 'product',
        'sku' => 'DELETE-001',
        'name' => 'Delete Product',
        'price' => 10,
        'currency_code' => 'EUR',
        'unit' => 'piece',
        'status' => 'active',
        'taxable' => false,
    ]);

    $this
        ->withToken($token)
        ->deleteJson('/api/v1/catalog-items/'.$item->public_id)
        ->assertNoContent();

    $this->assertDatabaseMissing('catalog_items', [
        'id' => $item->id,
    ]);
});

it('does not expose another tenant catalog item', function () {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $user = catalogUser($tenantA);

    catalogCurrency();

    $token = $user->createToken('test')->plainTextToken;

    $ownItem = CatalogItem::withoutGlobalScopes()->create([
        'tenant_id' => $tenantA->id,
        'public_id' => (string) Str::uuid(),
        'type' => 'product',
        'sku' => 'OWN-001',
        'name' => 'Own Product',
        'price' => 10,
        'currency_code' => 'EUR',
        'unit' => 'piece',
        'status' => 'active',
        'taxable' => false,
    ]);

    $foreignItem = CatalogItem::withoutGlobalScopes()->create([
        'tenant_id' => $tenantB->id,
        'public_id' => (string) Str::uuid(),
        'type' => 'product',
        'sku' => 'FOREIGN-001',
        'name' => 'Foreign Product',
        'price' => 20,
        'currency_code' => 'EUR',
        'unit' => 'piece',
        'status' => 'active',
        'taxable' => false,
    ]);

    $this
        ->withToken($token)
        ->getJson('/api/v1/catalog-items')
        ->assertOk()
        ->assertJsonFragment([
            'name' => $ownItem->name,
        ])
        ->assertJsonMissing([
            'name' => $foreignItem->name,
        ]);

    $this
        ->withToken($token)
        ->getJson('/api/v1/catalog-items/'.$foreignItem->public_id)
        ->assertNotFound();

    $this
        ->withToken($token)
        ->putJson('/api/v1/catalog-items/'.$foreignItem->public_id, [
            'type' => 'product',
            'sku' => 'HACKED',
            'name' => 'Hacked Product',
            'price' => 1,
            'currency_code' => 'EUR',
            'unit' => 'piece',
            'status' => 'active',
            'taxable' => false,
        ])
        ->assertNotFound();

    $this
        ->withToken($token)
        ->deleteJson('/api/v1/catalog-items/'.$foreignItem->public_id)
        ->assertNotFound();
});

it('requires catalog permission', function () {
    $tenant = Tenant::factory()->create();

    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'status' => UserStatus::Active,
    ]);

    $token = $user->createToken('test')->plainTextToken;

    $this
        ->withToken($token)
        ->getJson('/api/v1/catalog-items')
        ->assertForbidden();
});
