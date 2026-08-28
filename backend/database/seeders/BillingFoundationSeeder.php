<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class BillingFoundationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['code' => 'users.limit', 'name' => 'User limit', 'module_code' => 'core', 'type' => 'integer'], ['code' => 'branches.limit', 'name' => 'Branch limit', 'module_code' => 'core', 'type' => 'integer'], ['code' => 'api.access', 'name' => 'API access', 'module_code' => 'core', 'type' => 'boolean']] as $f) {
            Feature::query()->updateOrCreate(['code' => $f['code']], $f);
        }Plan::query()->firstOrCreate(['code' => 'starter'], ['public_id' => (string) Str::uuid(), 'name' => 'Starter', 'monthly_price' => 0, 'annual_price' => 0, 'currency_code' => 'USD', 'is_active' => true]);
    }
}
