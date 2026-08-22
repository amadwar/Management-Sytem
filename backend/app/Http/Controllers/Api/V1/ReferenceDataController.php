<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\Country;use App\Models\Currency;use App\Models\Language;use Illuminate\Http\JsonResponse;
final class ReferenceDataController extends Controller{public function index():JsonResponse{return response()->json(['data'=>['countries'=>Country::query()->where('is_active',true)->orderBy('name_en')->get(),'currencies'=>Currency::query()->where('is_active',true)->orderBy('code')->get(),'languages'=>Language::query()->where('is_active',true)->orderBy('code')->get()]]);}}
