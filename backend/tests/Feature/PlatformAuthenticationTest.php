<?php

declare(strict_types=1);

use App\Domain\Identity\Enums\UserStatus;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('authenticates a platform administrator', function () {
    $admin = User::factory()->create([
        'tenant_id' => null,
        'is_platform_admin' => true,
        'status' => UserStatus::Active,
        'email' => 'platform@example.com',
        'password' => Hash::make('Password123!'),
    ]);

    $this->postJson('/api/v1/platform/auth/login', [
        'email' => $admin->email,
        'password' => 'Password123!',
    ])->assertOk()
      ->assertJsonPath('data.user.email', $admin->email)
      ->assertJsonStructure(['data' => ['token', 'user']]);
});

it('does not allow a tenant user to use platform APIs', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create([
        'tenant_id' => $tenant->id,
        'is_platform_admin' => false,
        'status' => UserStatus::Active,
    ]);

    $token = $user->createToken('test')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/v1/platform/tenants')
        ->assertForbidden();
});
