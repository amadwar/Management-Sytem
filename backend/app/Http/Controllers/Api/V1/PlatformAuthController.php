<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PlatformLoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

final class PlatformAuthController extends Controller
{
    public function login(PlatformLoginRequest $request): JsonResponse
    {
        $user = User::withoutGlobalScopes()->whereNull('tenant_id')->where('is_platform_admin', true)->whereRaw('LOWER(email) = ?', [mb_strtolower($request->string('email')->toString())])->first();
        if ($user === null || !Hash::check($request->string('password')->toString(), $user->password)) {
            throw ValidationException::withMessages(['email'=>['Invalid credentials.']]);
        }
        $token = $user->createToken($request->string('device_name')->toString() ?: 'platform-web')->plainTextToken;
        return response()->json(['data'=>['token'=>$token,'user'=>UserResource::make($user)]]);
    }
    public function me(Request $request): UserResource
    {
        abort_unless($request->user()?->isPlatformAdmin(), 403);
        return new UserResource($request->user());
    }
    public function logout(Request $request): JsonResponse { $request->user()->currentAccessToken()?->delete(); return response()->json(['data'=>['logged_out'=>true]]); }
}
