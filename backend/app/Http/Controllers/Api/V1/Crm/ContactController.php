<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Crm;

use App\Application\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Crm\ContactRequest;
use App\Models\CrmContact;
use App\Models\CrmCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ContactController extends Controller
{
    public function store(ContactRequest $request, string $customer, AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('crm.customers.update'), 403);
        $parent = CrmCustomer::query()->where('public_id', $customer)->firstOrFail();
        $contact = DB::transaction(function () use ($request, $parent): CrmContact {
            if ($request->boolean('is_primary')) {
                CrmContact::query()->where('customer_id', $parent->id)->update(['is_primary' => false]);
            }

            return CrmContact::query()->create([
                'customer_id' => $parent->id, 'public_id' => (string) Str::uuid(), ...$request->validated(),
            ]);
        });
        $audit->record('crm.contact.created', $request, CrmContact::class, $contact->id);

        return response()->json(['data' => ['id' => $contact->public_id, 'name' => $contact->name, 'job_title' => $contact->job_title, 'email' => $contact->email, 'phone' => $contact->phone, 'is_primary' => $contact->is_primary]], 201);
    }

    public function destroy(Request $request, string $customer, string $contact, AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('crm.customers.update'), 403);
        $parent = CrmCustomer::query()->where('public_id', $customer)->firstOrFail();
        $model = CrmContact::query()->where('customer_id', $parent->id)->where('public_id', $contact)->firstOrFail();
        $audit->record('crm.contact.deleted', $request, CrmContact::class, $model->id);
        $model->delete();

        return response()->noContent();
    }
}
