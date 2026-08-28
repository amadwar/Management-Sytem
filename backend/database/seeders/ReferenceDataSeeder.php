<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Database\Seeder;

final class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        Language::query()->upsert([
            ['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl', 'is_active' => true],
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr', 'is_active' => true],
            ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'direction' => 'ltr', 'is_active' => true],
        ], ['code'], ['name', 'native_name', 'direction', 'is_active']);

        Currency::query()->upsert([
            ['code' => 'SYP', 'name' => 'Syrian Pound', 'symbol' => 'SYP', 'decimal_places' => 2, 'is_active' => true],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2, 'is_active' => true],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€', 'decimal_places' => 2, 'is_active' => true],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => 'SAR', 'decimal_places' => 2, 'is_active' => true],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'AED', 'decimal_places' => 2, 'is_active' => true],
        ], ['code'], ['name', 'symbol', 'decimal_places', 'is_active']);

        Country::query()->upsert([
            ['iso2' => 'SY', 'iso3' => 'SYR', 'name_en' => 'Syria', 'name_ar' => 'سوريا', 'phone_code' => '+963', 'is_active' => true],
            ['iso2' => 'DE', 'iso3' => 'DEU', 'name_en' => 'Germany', 'name_ar' => 'ألمانيا', 'phone_code' => '+49', 'is_active' => true],
            ['iso2' => 'SA', 'iso3' => 'SAU', 'name_en' => 'Saudi Arabia', 'name_ar' => 'السعودية', 'phone_code' => '+966', 'is_active' => true],
            ['iso2' => 'AE', 'iso3' => 'ARE', 'name_en' => 'United Arab Emirates', 'name_ar' => 'الإمارات العربية المتحدة', 'phone_code' => '+971', 'is_active' => true],
        ], ['iso2'], ['iso3', 'name_en', 'name_ar', 'phone_code', 'is_active']);
    }
}
