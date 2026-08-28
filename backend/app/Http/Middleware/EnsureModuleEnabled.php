<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Module;
use App\Models\TenantModule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureModuleEnabled
{
    public function handle(Request $request, Closure $next, string $moduleCode): Response
    {
        $module = Module::query()->where('code', $moduleCode)->where('is_active', true)->first();
        abort_if($module === null, 404, 'Module not found.');

        if (! $module->is_core) {
            $enabled = TenantModule::query()
                ->where('module_id', $module->id)
                ->where('enabled', true)
                ->exists();

            abort_unless($enabled, 403, 'This module is not enabled for the tenant.');
        }

        return $next($request);
    }
}
