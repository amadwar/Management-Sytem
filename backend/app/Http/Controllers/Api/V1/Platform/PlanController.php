<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class PlanController extends Controller
{
    private function admin(Request $r): void
    {
        abort_unless($r->user()?->isPlatformAdmin(), 403);
    }

    public function index(Request $r): JsonResponse
    {
        $this->admin($r);

        return response()->json(['data' => Plan::query()->orderBy('monthly_price')->get()]);
    }

    public function store(Request $r): JsonResponse
    {
        $this->admin($r);
        $d = $r->validate(['name' => ['required', 'string', 'max:150'], 'code' => ['required', 'alpha_dash', 'max:100', 'unique:plans,code'], 'monthly_price' => ['required', 'numeric', 'min:0'], 'annual_price' => ['required', 'numeric', 'min:0'], 'currency_code' => ['required', 'string', 'size:3']]);
        $p = Plan::query()->create([...$d, 'public_id' => (string) Str::uuid(), 'is_active' => true]);

        return response()->json(['data' => $p], 201);
    }

    public function update(Request $r, string $plan): JsonResponse
    {
        $this->admin($r);
        $p = Plan::query()->where('public_id', $plan)->firstOrFail();
        $d = $r->validate(['name' => ['sometimes', 'string', 'max:150'], 'monthly_price' => ['sometimes', 'numeric', 'min:0'], 'annual_price' => ['sometimes', 'numeric', 'min:0'], 'currency_code' => ['sometimes', 'string', 'size:3'], 'is_active' => ['sometimes', 'boolean']]);
        $p->update($d);

        return response()->json(['data' => $p]);
    }
}
