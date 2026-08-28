<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([ReferenceDataSeeder::class, AuthorizationSeeder::class, ModuleSeeder::class, BillingFoundationSeeder::class, PlatformAdminSeeder::class]);
    }
}
