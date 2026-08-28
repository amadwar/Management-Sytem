<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RoleRequest;
use App\Http\Resources\Api\V1\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RoleController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return RoleResource::collection(Role::query()->with('permissions')->latest()->paginate(50));
    }

    public function store(RoleRequest $request, AuditLogger $audit): RoleResource
    {
        abort_unless($request->user()->hasPermission('roles.create'), 403);
        $this->assertUniqueCode($request->string('code')->toString());
        $role = Role::query()->create(['public_id' => (string) Str::uuid(), 'name' => $request->string('name')->toString(), 'code' => $request->string('code')->toString(), 'is_system' => false]);
        $role->permissions()->sync(Permission::query()->whereIn('id', $request->input('permission_ids', []))->pluck('id'));
        $audit->record('role.created', $request, Role::class, $role->id);

        return new RoleResource($role->load('permissions'));
    }

    public function show(string $role): RoleResource
    {
        return new RoleResource($this->findRole($role)->load('permissions'));
    }

    public function update(RoleRequest $request, string $role, AuditLogger $audit): RoleResource
    {
        abort_unless($request->user()->hasPermission('roles.update'), 403);
        $model = $this->findRole($role);
        abort_if($model->is_system, 422, 'System roles cannot be edited.');
        $this->assertUniqueCode($request->string('code')->toString(), $model->id);
        $model->update(['name' => $request->string('name')->toString(), 'code' => $request->string('code')->toString()]);
        $model->permissions()->sync(Permission::query()->whereIn('id', $request->input('permission_ids', []))->pluck('id'));
        $audit->record('role.updated', $request, Role::class, $model->id);

        return new RoleResource($model->load('permissions'));
    }

    public function destroy(Request $request, string $role, AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('roles.delete'), 403);
        $model = $this->findRole($role);
        abort_if($model->is_system, 422, 'System roles cannot be deleted.');
        abort_if($model->users()->exists(), 422, 'Role is assigned to users.');
        $audit->record('role.deleted', $request, Role::class, $model->id);
        $model->delete();

        return response()->noContent();
    }

    private function findRole(string $publicId): Role
    {
        return Role::query()->where('public_id', $publicId)->firstOrFail();
    }

    private function assertUniqueCode(string $code, ?int $ignoreId = null): void
    {
        $query = Role::query()->where('code', $code);
        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }
        if ($query->exists()) {
            throw ValidationException::withMessages(['code' => ['Role code is already used in this workspace.']]);
        }
    }
}
