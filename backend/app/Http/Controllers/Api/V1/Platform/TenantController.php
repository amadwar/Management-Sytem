<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Platform;

use App\Application\Audit\AuditLogger;
use App\Application\Tenancy\Actions\CreateTenantAction;
use App\Application\Tenancy\Data\CreateTenantData;
use App\Domain\Tenancy\Enums\TenantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateTenantRequest;
use App\Http\Resources\Api\V1\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class TenantController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        abort_unless($request->user()?->isPlatformAdmin(), 403);
        return TenantResource::collection(Tenant::query()->with('organization')->latest()->paginate(20));
    }

    public function store(CreateTenantRequest $request, CreateTenantAction $action, AuditLogger $audit): TenantResource
    {
        $tenant = $action->execute(new CreateTenantData(
            slug: $request->string('slug')->toString(),
            legalName: $request->string('legal_name')->toString(),
            displayName: $request->string('display_name')->toString(),
            organizationEmail: $request->string('organization_email')->toString(),
            ownerName: $request->string('owner_name')->toString(),
            ownerEmail: $request->string('owner_email')->toString(),
            ownerPassword: $request->string('owner_password')->toString(),
            timezone: $request->string('timezone')->toString() ?: 'UTC',
            locale: $request->string('locale')->toString() ?: 'en',
        ));
        $audit->record('platform.tenant.created', $request, Tenant::class, $tenant->id, ['public_id'=>$tenant->public_id]);
        return new TenantResource($tenant);
    }

    public function show(Request $request, Tenant $tenant): TenantResource
    {
        abort_unless($request->user()?->isPlatformAdmin(), 403);
        return new TenantResource($tenant->load('organization'));
    }

    public function update(Request $request, Tenant $tenant, AuditLogger $audit): TenantResource
    {
        abort_unless($request->user()?->isPlatformAdmin(), 403);
        $data = $request->validate(['status'=>['sometimes', 'in:pending,active,suspended'], 'timezone'=>['sometimes','timezone'], 'default_locale'=>['sometimes','in:ar,en,de']]);
        if (isset($data['status'])) { $data['status'] = TenantStatus::from($data['status']); }
        $tenant->update($data);
        $audit->record('platform.tenant.updated', $request, Tenant::class, $tenant->id, ['changes'=>array_keys($data)]);
        return new TenantResource($tenant->load('organization'));
    }
}
