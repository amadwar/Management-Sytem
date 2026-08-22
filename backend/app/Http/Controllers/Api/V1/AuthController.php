<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Domain\Identity\Enums\UserStatus;
use App\Domain\Tenancy\Enums\TenantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $tenant = Tenant::query()->where('slug', $request->string('workspace'))->first();

        if ($tenant === null || $tenant->status !== TenantStatus::Active) {
            throw ValidationException::withMessages(['workspace' => ['Invalid workspace or credentials.']]);
        }

        $user = User::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($request->string('email')->toString())])
            ->first();

        if ($user === null || $user->status !== UserStatus::Active || !Hash::check($request->string('password')->toString(), $user->password)) {
            throw ValidationException::withMessages(['email' => ['Invalid workspace or credentials.']]);
        }

        $user->forceFill(['last_login_at' => now()])->save();
        $token = $user->createToken($request->string('device_name')->toString() ?: 'web')->plainTextToken;

        return response()->json(['data' => ['token' => $token, 'user' => UserResource::make($user->load('roles'))]]);
    }

    public function me(Request $request): UserResource { return new UserResource($request->user()->load('roles')); }
    public function logout(Request $request): JsonResponse { $request->user()->currentAccessToken()?->delete(); return response()->json(['data'=>['logged_out'=>true]]); }
}
