<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Audit\AuditLogger;
use App\Domain\Identity\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return UserResource::collection(User::query()->with('roles')->latest()->paginate(20));
    }

    public function store(UserRequest $request, AuditLogger $audit): UserResource
    {
        abort_unless($request->user()->hasPermission('users.create'), 403);
        $this->assertUniqueEmail($request->string('email')->toString());
        $data = Arr::except($request->validated(), ['role_ids']);
        $user = User::query()->create([...$data, 'email' => mb_strtolower($data['email']), 'public_id' => (string) Str::uuid(), 'status' => UserStatus::Active, 'is_platform_admin' => false]);
        $roleIds = Role::query()->whereIn('id', $request->input('role_ids', []))->pluck('id');
        $user->roles()->sync($roleIds);
        $audit->record('user.created', $request, User::class, $user->id);

        return new UserResource($user->load('roles'));
    }

    public function show(string $user): UserResource
    {
        return new UserResource($this->findUser($user)->load('roles'));
    }

    public function update(UserRequest $request, string $user, AuditLogger $audit): UserResource
    {
        abort_unless($request->user()->hasPermission('users.update'), 403);
        $model = $this->findUser($user);
        $this->assertUniqueEmail($request->string('email')->toString(), $model->id);
        $data = Arr::except($request->validated(), ['role_ids']);
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['email'] = mb_strtolower($data['email']);
        $model->update($data);
        if ($request->has('role_ids')) {
            $model->roles()->sync(Role::query()->whereIn('id', $request->input('role_ids', []))->pluck('id'));
        }
        $audit->record('user.updated', $request, User::class, $model->id);

        return new UserResource($model->load('roles'));
    }

    public function destroy(Request $request, string $user, AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('users.delete'), 403);
        $model = $this->findUser($user);
        abort_if($request->user()->is($model), 422, 'You cannot delete your own account.');
        $audit->record('user.deleted', $request, User::class, $model->id);
        $model->delete();

        return response()->noContent();
    }

    private function findUser(string $publicId): User
    {
        return User::query()->where('public_id', $publicId)->firstOrFail();
    }

    private function assertUniqueEmail(string $email, ?int $ignoreId = null): void
    {
        $query = User::query()->whereRaw('LOWER(email) = ?', [mb_strtolower($email)]);
        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }
        if ($query->exists()) {
            throw ValidationException::withMessages(['email' => ['This email is already used in this workspace.']]);
        }
    }
}
