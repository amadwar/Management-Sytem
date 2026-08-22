<?php

declare(strict_types=1);
namespace App\Http\Controllers\Api\V1;
use App\Application\Audit\AuditLogger;use App\Http\Controllers\Controller;use App\Models\TenantSetting;use Illuminate\Http\JsonResponse;use Illuminate\Http\Request;
final class SettingsController extends Controller{public function index():JsonResponse{$data=TenantSetting::query()->pluck('value','key');return response()->json(['data'=>$data]);}public function update(Request $request,AuditLogger $audit):JsonResponse{$validated=$request->validate(['settings'=>['required','array'],'settings.*'=>['nullable']]);foreach($validated['settings'] as $key=>$value){abort_unless(is_string($key)&&strlen($key)<=150,422,'Invalid setting key.');TenantSetting::query()->updateOrCreate(['key'=>$key],['value'=>$value]);}$audit->record('settings.updated',$request,metadata:['keys'=>array_keys($validated['settings'])]);return $this->index();}}
