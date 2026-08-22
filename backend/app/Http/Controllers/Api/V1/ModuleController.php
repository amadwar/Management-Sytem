<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Audit\AuditLogger;
use App\Domain\Tenancy\TenantContext;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\TenantModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ModuleController extends Controller
{
    public function index(TenantContext $context): JsonResponse
    {
        $rows=Module::query()->where('is_active',true)->orderBy('name')->get()->map(function(Module $module) use ($context) {
            $activation=TenantModule::query()->where('module_id',$module->id)->first();
            return ['code'=>$module->code,'name'=>$module->name,'description'=>$module->description,'is_core'=>$module->is_core,'enabled'=>$activation?->enabled ?? false];
        });
        return response()->json(['data'=>$rows]);
    }
    public function updateActivation(Request $request, Module $module, TenantContext $context, AuditLogger $audit): JsonResponse
    {
        $data=$request->validate(['enabled'=>['required','boolean']]); abort_if($module->is_core && !$data['enabled'],422,'Core modules cannot be disabled.');
        $activation=TenantModule::query()->updateOrCreate(['module_id'=>$module->id],['enabled'=>$data['enabled']]);
        $audit->record('module.activation.updated',$request,Module::class,$module->id,['enabled'=>$activation->enabled]);
        return response()->json(['data'=>['code'=>$module->code,'enabled'=>$activation->enabled]]);
    }
}
