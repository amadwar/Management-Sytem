<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BranchRequest;
use App\Http\Resources\Api\V1\BranchResource;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

final class BranchController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return BranchResource::collection(Branch::query()->latest()->paginate(20));
    }

    public function store(BranchRequest $request, AuditLogger $audit): BranchResource
    {
        abort_unless($request->user()->hasPermission('branches.create'), 403);
        $branch = Branch::query()->create(['public_id' => (string) Str::uuid(), ...$request->validated()]);
        $audit->record('branch.created', $request, Branch::class, $branch->id);

        return new BranchResource($branch);
    }

    public function show(string $branch): BranchResource
    {
        return new BranchResource($this->findBranch($branch));
    }

    public function update(BranchRequest $request, string $branch, AuditLogger $audit): BranchResource
    {
        abort_unless($request->user()->hasPermission('branches.update'), 403);
        $model = $this->findBranch($branch);
        $model->update($request->validated());
        $audit->record('branch.updated', $request, Branch::class, $model->id);

        return new BranchResource($model);
    }

    public function destroy(Request $request, string $branch, AuditLogger $audit)
    {
        abort_unless($request->user()->hasPermission('branches.delete'), 403);
        $model = $this->findBranch($branch);
        $audit->record('branch.deleted', $request, Branch::class, $model->id);
        $model->delete();

        return response()->noContent();
    }

    private function findBranch(string $publicId): Branch
    {
        // This query runs after ResolveTenant, so the global tenant scope is active.
        return Branch::query()->where('public_id', $publicId)->firstOrFail();
    }
}
