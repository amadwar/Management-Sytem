<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Tenancy\Enums\TenantStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

final class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $slug = $this->faker->unique()->slug(2);

        return ['public_id' => (string) Str::uuid(), 'slug' => $slug, 'status' => TenantStatus::Active, 'default_locale' => 'en', 'timezone' => 'UTC'];
    }
}
