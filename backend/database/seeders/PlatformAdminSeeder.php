<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Identity\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class PlatformAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) env('PLATFORM_ADMIN_EMAIL', 'admin@example.com');
        User::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => null, 'email' => $email],
            ['public_id' => (string) Str::uuid(), 'name' => (string) env('PLATFORM_ADMIN_NAME', 'Platform Admin'), 'password' => (string) env('PLATFORM_ADMIN_PASSWORD', 'ChangeMe123!'), 'status' => UserStatus::Active, 'is_platform_admin' => true]
        );
    }
}
