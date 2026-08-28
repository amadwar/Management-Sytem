<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Crm;

use App\Application\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Crm\CustomerRequest;
use App\Http\Resources\Api\V1\Crm\CustomerResource;
use App\Models\CrmCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

final class CustomerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = CrmCustomer::query()->with(['tags','contacts'])->latest();

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'ilike', "%{$search}%")
                    ->orWhere('company_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'ilike', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) { $query->where('type', $type); }
        if ($status = $request->query('status')) { $query->where('status', $status); }

        return CustomerResource::collection($query->paginate(min((int) $request->query('per_page', 20), 100)));
    }

    public function store(CustomerRequest $request, AuditLogger $audit): CustomerResource
    {
        abort_unless($request->user()->hasPermission('crm.customers.create'), 403);
        $data = $request->validated();
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);
        $customer = CrmCustomer::query()->create(['public_id'=>(string) Str::uuid(), ...$data]);
        if ($tagIds !== []) { $customer->tags()->sync($tagIds); }
        $audit->record('crm.customer.created', $request, CrmCustomer::class, $customer->id);
        return new CustomerResource($customer->load(['tags','contacts']));
    }

    public function show(string $customer): CustomerResource
    {
        return new CustomerResource($this->findCustomer($customer)->load(['tags','contacts']));
    }

    public function update(CustomerRequest $request, string $customer, AuditLogger $audit): CustomerResource
    {
        abort_unless($request->user()->hasPermission('crm.customers.update'), 403);
        $model = $this->findCustomer($customer);
        $data = $request->validated();
        $tagIds = $data['tag_ids'] ?? null;
        unset($data['tag_ids']);
        $model->update($data);
        if ($tagIds !== null) { $model->tags()->sync($tagIds); }
        $audit->record('crm.customer.updated', $request, CrmCustomer::class, $model->id);
        return new CustomerResource($model->load(['tags','contacts']));
    }

    public function destroy(Request $request, string $customer, AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('crm.customers.delete'), 403);
        $model = $this->findCustomer($customer);
        $audit->record('crm.customer.deleted', $request, CrmCustomer::class, $model->id);
        $model->delete();
        return response()->noContent();
    }

    private function findCustomer(string $publicId): CrmCustomer
    {
        return CrmCustomer::query()->where('public_id', $publicId)->firstOrFail();
    }
}
