<?php

declare(strict_types=1);

use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\EnsureTenantUser;
use App\Http\Middleware\EnsureModuleEnabled;
use App\Http\Middleware\EnsurePlatformAdmin;
use App\Http\Middleware\ResolveTenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant.user' => EnsureTenantUser::class,
            'tenant.resolve' => ResolveTenant::class,
            'permission' => EnsurePermission::class,
            'module' => EnsureModuleEnabled::class,
            'platform.admin' => EnsurePlatformAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request, Throwable $e): bool => $request->is('api/*')
        );
    })
    ->create();
