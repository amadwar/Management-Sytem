<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()?->hasPermission($permission)) {
            abort(403, 'Missing required permission.');
        }

        return $next($request);
    }
}
