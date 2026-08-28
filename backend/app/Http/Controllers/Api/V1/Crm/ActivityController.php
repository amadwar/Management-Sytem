<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Crm;

use App\Application\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Crm\ActivityRequest;
use App\Models\CrmActivity;
use App\Models\CrmCustomer;
use Illuminate\Support\Str;

final class ActivityController extends Controller
{
    public function index(string $customer)
    {
        $parent = CrmCustomer::query()->where('public_id', $customer)->firstOrFail();

        return response()->json(['data' => CrmActivity::query()->where('customer_id', $parent->id)->with('user:id,name')->latest('occurred_at')->get()->map(fn ($a) => ['id' => $a->public_id, 'type' => $a->type->value, 'subject' => $a->subject, 'description' => $a->description, 'occurred_at' => $a->occurred_at?->toISOString(), 'user' => $a->user?->name])]);
    }

    public function store(ActivityRequest $request, string $customer, AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('crm.activities.create'), 403);
        $parent = CrmCustomer::query()->where('public_id', $customer)->firstOrFail();
        $activity = CrmActivity::query()->create(['customer_id' => $parent->id, 'user_id' => $request->user()->id, 'public_id' => (string) Str::uuid(), ...$request->validated()]);
        $audit->record('crm.activity.created', $request, CrmActivity::class, $activity->id);

        return response()->json(['data' => ['id' => $activity->public_id]], 201);
    }
}
