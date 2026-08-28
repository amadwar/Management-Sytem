<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Crm;

use App\Application\Audit\AuditLogger;
use App\Domain\Crm\Enums\CustomerStatus;
use App\Domain\Crm\Enums\CustomerType;
use App\Domain\Crm\Enums\LeadStage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Crm\LeadRequest;
use App\Http\Resources\Api\V1\Crm\LeadResource;
use App\Models\CrmCustomer;
use App\Models\CrmLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = CrmLead::query()->latest();
        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(fn ($q) => $q->where('name', 'ilike', "%{$search}%")->orWhere('company_name', 'ilike', "%{$search}%")->orWhere('email', 'ilike', "%{$search}%"));
        }
        if ($stage = $request->query('stage')) {
            $query->where('stage', $stage);
        }

        return LeadResource::collection($query->paginate(20));
    }

    public function store(LeadRequest $request, AuditLogger $audit): LeadResource
    {
        abort_unless($request->user()->hasPermission('crm.leads.create'), 403);
        $lead = CrmLead::query()->create(['public_id' => (string) Str::uuid(), ...$request->validated()]);
        $audit->record('crm.lead.created', $request, CrmLead::class, $lead->id);

        return new LeadResource($lead);
    }

    public function update(LeadRequest $request, string $lead, AuditLogger $audit): LeadResource
    {
        abort_unless($request->user()->hasPermission('crm.leads.update'), 403);
        $model = $this->findLead($lead);
        $model->update($request->validated());
        $audit->record('crm.lead.updated', $request, CrmLead::class, $model->id);

        return new LeadResource($model);
    }

    public function convert(Request $request, string $lead, AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('crm.leads.convert'), 403);
        $model = $this->findLead($lead);
        abort_if($model->converted_at !== null, 422, 'Lead is already converted.');
        $customer = DB::transaction(function () use ($model) {
            $customer = CrmCustomer::query()->create([
                'public_id' => (string) Str::uuid(), 'type' => CustomerType::Company, 'status' => CustomerStatus::Active,
                'name' => $model->name, 'company_name' => $model->company_name, 'email' => $model->email, 'phone' => $model->phone,
                'source' => $model->source, 'assigned_to' => $model->assigned_to,
            ]);
            $model->update(['stage' => LeadStage::Won, 'converted_at' => now(), 'converted_customer_id' => $customer->id]);

            return $customer;
        });
        $audit->record('crm.lead.converted', $request, CrmLead::class, $model->id, ['customer_id' => $customer->id]);

        return response()->json(['data' => ['customer_id' => $customer->public_id]]);
    }

    private function findLead(string $publicId): CrmLead
    {
        return CrmLead::query()->where('public_id',$publicId)->firstOrFail();
    }
}
