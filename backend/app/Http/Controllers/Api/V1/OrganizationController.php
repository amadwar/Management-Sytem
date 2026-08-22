<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\Request;

final class OrganizationController extends Controller
{
    public function show(): OrganizationResource { return new OrganizationResource(Organization::query()->firstOrFail()); }
    public function update(Request $request, AuditLogger $audit): OrganizationResource
    {
        $organization=Organization::query()->firstOrFail();
        $data=$request->validate(['legal_name'=>['sometimes','string','max:255'],'display_name'=>['sometimes','string','max:255'],'email'=>['sometimes','email'],'phone'=>['nullable','string','max:50'],'tax_number'=>['nullable','string','max:100'],'address'=>['nullable','string','max:1000']]);
        $organization->update($data); $audit->record('organization.updated',$request,Organization::class,$organization->id,['changes'=>array_keys($data)]);
        return new OrganizationResource($organization);
    }
}
