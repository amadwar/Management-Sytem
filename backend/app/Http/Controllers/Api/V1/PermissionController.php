<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

final class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Permission::query()->orderBy('module_code')->orderBy('code')->get(['id', 'code', 'name', 'module_code'])]);
    }
}
