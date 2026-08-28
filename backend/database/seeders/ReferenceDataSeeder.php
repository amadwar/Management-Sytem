<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Database\Seeder;

final class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Languages
        |--------------------------------------------------------------------------
        */

        Language::query()->upsert([
            [
                'code' => 'ar',
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'direction' => 'rtl',
                'is_active' => true,
            ],
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
                'direction' => 'ltr',
                'is_active' => true,
            ],
            [
                'code' => 'de',
                'name' => 'German',
                'native_name' => 'Deutsch',
                'direction' => 'ltr',
                'is_active' => true,
            ],
        ], ['code'], [
            'name',
            'native_name',
            'direction',
            'is_active',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Currencies
        |--------------------------------------------------------------------------
        */

        Currency::query()->upsert([
            [
                'code' => 'SYP',
                'name' => 'Syrian Pound',
                'symbol' => 'SYP',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'SAR',
                'name' => 'Saudi Riyal',
                'symbol' => 'SAR',
                'decimal_places' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'AED',
                'name' => 'UAE Dirham',
                'symbol' => 'AED',
                'decimal_places' => 2,
                'is_active' => true,
            ],
        ], ['code'], [
            'name',
            'symbol',
            'decimal_places',
            'is_active',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Countries
        |--------------------------------------------------------------------------
        */

        Country::query()->upsert([
            [
                'iso2' => 'SY',
                'iso3' => 'SYR',
                'name_en' => 'Syria',
                'name_ar' => 'سوريا',
                'phone_code' => '+963',
                'is_active' => true,
            ],
            [
                'iso2' => 'DE',
                'iso3' => 'DEU',
                'name_en' => 'Germany',
                'name_ar' => 'ألمانيا',
                'phone_code' => '+49',
                'is_active' => true,
            ],
            [
                'iso2' => 'SA',
                'iso3' => 'SAU',
                'name_en' => 'Saudi Arabia',
                'name_ar' => 'السعودية',
                'phone_code' => '+966',
                'is_active' => true,
            ],
            [
                'iso2' => 'AE',
                'iso3' => 'ARE',
                'name_en' => 'United Arab Emirates',
                'name_ar' => 'الإمارات العربية المتحدة',
                'phone_code' => '+971',
                'is_active' => true,
            ],
        ], ['iso2'], [
            'iso3',
            'name_en',
            'name_ar',
            'phone_code',
            'is_active',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cities
        |--------------------------------------------------------------------------
        */

        $cities = [
            'SY' => [
                ['name_en' => 'Damascus', 'name_ar' => 'دمشق'],
                ['name_en' => 'Aleppo', 'name_ar' => 'حلب'],
                ['name_en' => 'Homs', 'name_ar' => 'حمص'],
                ['name_en' => 'Hama', 'name_ar' => 'حماة'],
                ['name_en' => 'Latakia', 'name_ar' => 'اللاذقية'],
                ['name_en' => 'Tartus', 'name_ar' => 'طرطوس'],
                ['name_en' => 'Daraa', 'name_ar' => 'درعا'],
                ['name_en' => 'As-Suwayda', 'name_ar' => 'السويداء'],
                ['name_en' => 'Idlib', 'name_ar' => 'إدلب'],
                ['name_en' => 'Deir ez-Zor', 'name_ar' => 'دير الزور'],
                ['name_en' => 'Raqqa', 'name_ar' => 'الرقة'],
                ['name_en' => 'Al-Hasakah', 'name_ar' => 'الحسكة'],
                ['name_en' => 'Quneitra', 'name_ar' => 'القنيطرة'],
                ['name_en' => 'Rif Dimashq', 'name_ar' => 'ريف دمشق'],
            ],

            'DE' => [
                ['name_en' => 'Berlin', 'name_ar' => 'برلين'],
                ['name_en' => 'Hamburg', 'name_ar' => 'هامبورغ'],
                ['name_en' => 'Munich', 'name_ar' => 'ميونخ'],
                ['name_en' => 'Cologne', 'name_ar' => 'كولونيا'],
                ['name_en' => 'Frankfurt', 'name_ar' => 'فرانكفورت'],
                ['name_en' => 'Stuttgart', 'name_ar' => 'شتوتغارت'],
                ['name_en' => 'Düsseldorf', 'name_ar' => 'دوسلدورف'],
                ['name_en' => 'Dortmund', 'name_ar' => 'دورتموند'],
                ['name_en' => 'Essen', 'name_ar' => 'إيسن'],
                ['name_en' => 'Bremen', 'name_ar' => 'بريمن'],
                ['name_en' => 'Hanover', 'name_ar' => 'هانوفر'],
                ['name_en' => 'Nuremberg', 'name_ar' => 'نورنبيرغ'],
            ],

            'SA' => [
                ['name_en' => 'Riyadh', 'name_ar' => 'الرياض'],
                ['name_en' => 'Jeddah', 'name_ar' => 'جدة'],
                ['name_en' => 'Mecca', 'name_ar' => 'مكة المكرمة'],
                ['name_en' => 'Medina', 'name_ar' => 'المدينة المنورة'],
                ['name_en' => 'Dammam', 'name_ar' => 'الدمام'],
                ['name_en' => 'Khobar', 'name_ar' => 'الخبر'],
                ['name_en' => 'Taif', 'name_ar' => 'الطائف'],
                ['name_en' => 'Tabuk', 'name_ar' => 'تبوك'],
            ],

            'AE' => [
                ['name_en' => 'Abu Dhabi', 'name_ar' => 'أبوظبي'],
                ['name_en' => 'Dubai', 'name_ar' => 'دبي'],
                ['name_en' => 'Sharjah', 'name_ar' => 'الشارقة'],
                ['name_en' => 'Ajman', 'name_ar' => 'عجمان'],
                ['name_en' => 'Umm Al Quwain', 'name_ar' => 'أم القيوين'],
                ['name_en' => 'Ras Al Khaimah', 'name_ar' => 'رأس الخيمة'],
                ['name_en' => 'Fujairah', 'name_ar' => 'الفجيرة'],
            ],
        ];

        foreach ($cities as $countryCode => $countryCities) {
            $country = Country::query()
                ->where('iso2', $countryCode)
                ->firstOrFail();

            foreach ($countryCities as $city) {
                City::query()->updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'name_en' => $city['name_en'],
                    ],
                    [
                        'name_ar' => $city['name_ar'],
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
