<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AuditController;
use App\Http\Controllers\Api\V1\BranchController;
use App\Http\Controllers\Api\V1\ModuleController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\ReferenceDataController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\Platform\TenantController as PlatformTenantController;
use App\Http\Controllers\Api\V1\Platform\PlanController as PlatformPlanController;
use App\Http\Controllers\Api\V1\PlatformAuthController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('health', fn () => ['data' => ['status' => 'ok']]);

    Route::prefix('auth')->group(function (): void {
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1');
        Route::middleware('auth:sanctum')->group(function (): void {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });

    Route::prefix('platform')->group(function (): void {
        Route::post('auth/login', [PlatformAuthController::class, 'login'])->middleware('throttle:10,1');

        Route::middleware(['auth:sanctum'])->group(function (): void {
            Route::get('auth/me', [PlatformAuthController::class, 'me']);
            Route::post('auth/logout', [PlatformAuthController::class, 'logout']);
            Route::apiResource('tenants', PlatformTenantController::class)->only(['index', 'store', 'show', 'update']);
            Route::apiResource('plans', PlatformPlanController::class)->only(['index','store','update']);
        });
    });

    Route::middleware(['auth:sanctum', 'tenant.user', 'tenant.resolve'])->group(function (): void {
        Route::get('reference-data', [ReferenceDataController::class, 'index']);
        Route::get('organization', [OrganizationController::class, 'show']);
        Route::put('organization', [OrganizationController::class, 'update'])->middleware('permission:organization.update');

        Route::apiResource('branches', BranchController::class)->middleware('permission:branches.view');
        Route::apiResource('users', UserController::class)->middleware('permission:users.view');
        Route::apiResource('roles', RoleController::class)->middleware('permission:roles.view');

        Route::get('permissions', [PermissionController::class, 'index'])->middleware('permission:roles.view');
        Route::get('audit-logs', [AuditController::class, 'index'])->middleware('permission:audit.view');
        Route::get('settings', [SettingsController::class, 'index']);
        Route::put('settings', [SettingsController::class, 'update'])->middleware('permission:organization.update');

        Route::get('modules', [ModuleController::class, 'index']);
        Route::put('modules/{module}/activation', [ModuleController::class, 'updateActivation'])
            ->middleware('permission:modules.manage');
    });
});
