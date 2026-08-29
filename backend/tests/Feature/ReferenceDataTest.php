<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Identity\Enums\UserStatus;
use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ReferenceDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_active_reference_data_for_authenticated_tenant_user(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => UserStatus::Active,
        ]);

        Language::query()->create([
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'is_active' => true,
        ]);

        Language::query()->create([
            'code' => 'xx',
            'name' => 'Inactive Language',
            'native_name' => 'Inactive',
            'direction' => 'ltr',
            'is_active' => false,
        ]);

        Currency::query()->create([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'decimal_places' => 2,
            'is_active' => true,
        ]);

        Currency::query()->create([
            'code' => 'XXX',
            'name' => 'Inactive Currency',
            'symbol' => 'X',
            'decimal_places' => 2,
            'is_active' => false,
        ]);

        Country::query()->create([
            'iso2' => 'DE',
            'iso3' => 'DEU',
            'name_en' => 'Germany',
            'name_ar' => 'ألمانيا',
            'phone_code' => '+49',
            'is_active' => true,
        ]);

        Country::query()->create([
            'iso2' => 'ZZ',
            'iso3' => 'ZZZ',
            'name_en' => 'Inactive Country',
            'name_ar' => 'دولة غير فعالة',
            'phone_code' => '+000',
            'is_active' => false,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->getJson('/api/v1/reference-data');

        $response
            ->assertOk()
            ->assertJsonPath('data.languages.0.code', 'en')
            ->assertJsonPath('data.currencies.0.code', 'EUR')
            ->assertJsonPath('data.countries.0.iso2', 'DE')
            ->assertJsonMissing([
                'code' => 'xx',
            ])
            ->assertJsonMissing([
                'code' => 'XXX',
            ])
            ->assertJsonMissing([
                'iso2' => 'ZZ',
            ]);
    }

    public function test_it_returns_only_active_cities_for_requested_country(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => UserStatus::Active,
        ]);

        $germany = Country::query()->create([
            'iso2' => 'DE',
            'iso3' => 'DEU',
            'name_en' => 'Germany',
            'name_ar' => 'ألمانيا',
            'phone_code' => '+49',
            'is_active' => true,
        ]);

        $syria = Country::query()->create([
            'iso2' => 'SY',
            'iso3' => 'SYR',
            'name_en' => 'Syria',
            'name_ar' => 'سوريا',
            'phone_code' => '+963',
            'is_active' => true,
        ]);

        City::query()->create([
            'country_id' => $germany->id,
            'name_en' => 'Berlin',
            'name_ar' => 'برلين',
            'is_active' => true,
        ]);

        City::query()->create([
            'country_id' => $germany->id,
            'name_en' => 'Inactive City',
            'name_ar' => 'مدينة غير فعالة',
            'is_active' => false,
        ]);

        City::query()->create([
            'country_id' => $syria->id,
            'name_en' => 'Damascus',
            'name_ar' => 'دمشق',
            'is_active' => true,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this
            ->withToken($token)
            ->getJson(
                '/api/v1/reference-data/cities?country_id='.$germany->id
            );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name_en', 'Berlin')
            ->assertJsonPath('data.0.name_ar', 'برلين')
            ->assertJsonMissing([
                'name_en' => 'Damascus',
            ])
            ->assertJsonMissing([
                'name_en' => 'Inactive City',
            ]);
    }

    public function test_city_endpoint_requires_country_id(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
            'status' => UserStatus::Active,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $this
            ->withToken($token)
            ->getJson('/api/v1/reference-data/cities')
            ->assertUnprocessable()
            ->assertJsonValidationErrors('country_id');
    }
}
