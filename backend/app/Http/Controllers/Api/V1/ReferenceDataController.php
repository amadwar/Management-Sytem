<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReferenceDataController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'countries' => Country::query()
                    ->where('is_active', true)
                    ->orderBy('name_en')
                    ->get(),

                'currencies' => Currency::query()
                    ->where('is_active', true)
                    ->orderBy('code')
                    ->get(),

                'languages' => Language::query()
                    ->where('is_active', true)
                    ->orderBy('code')
                    ->get(),
            ],
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => [
                'required',
                'integer',
                'exists:countries,id',
            ],
        ]);

        $cities = City::query()
            ->where('country_id', $validated['country_id'])
            ->where('is_active', true)
            ->orderBy('name_en')
            ->get();

        return response()->json([
            'data' => $cities,
        ]);
    }
}
