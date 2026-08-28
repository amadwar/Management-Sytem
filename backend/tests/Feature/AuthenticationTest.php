<?php

use App\Domain\Identity\Enums\UserStatus;
use App\Models\Tenant;
use App\Models\User;

it('authenticates a tenant user by workspace email and password', function () {
    $tenant = Tenant::factory()->create(['slug' => 'acme']);
    User::factory()->create(['tenant_id' => $tenant->id, 'email' => 'owner@acme.test', 'password' => 'SuperSecret123!', 'status' => UserStatus::Active]);
    $this->postJson('/api/v1/auth/login', ['workspace' => 'acme', 'email' => 'owner@acme.test', 'password' => 'SuperSecret123!'])->assertOk()->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email']]]);
});
