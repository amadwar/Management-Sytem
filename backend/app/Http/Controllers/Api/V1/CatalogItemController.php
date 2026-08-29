<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Application\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CatalogItemRequest;
use App\Http\Resources\Api\V1\CatalogItemResource;
use App\Models\CatalogItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

final class CatalogItemController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = CatalogItem::query()->latest();

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'ilike', "%{$search}%")
                    ->orWhere('sku', 'ilike', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return CatalogItemResource::collection(
            $query->paginate(
                min((int) $request->query('per_page', 20), 100)
            )
        );
    }

    public function store(
        CatalogItemRequest $request,
        AuditLogger $audit
    ): CatalogItemResource {
        abort_unless(
            $request->user()->hasPermission('catalog.create'),
            403
        );

        $item = CatalogItem::query()->create([
            'public_id' => (string) Str::uuid(),
            ...$request->validated(),
        ]);

        $audit->record(
            'catalog.item.created',
            $request,
            CatalogItem::class,
            $item->id
        );

        return new CatalogItemResource($item);
    }

    public function show(string $catalogItem): CatalogItemResource
    {
        return new CatalogItemResource(
            $this->findItem($catalogItem)
        );
    }

    public function update(
        CatalogItemRequest $request,
        string $catalogItem,
        AuditLogger $audit
    ): CatalogItemResource {
        abort_unless(
            $request->user()->hasPermission('catalog.update'),
            403
        );

        $item = $this->findItem($catalogItem);

        $item->update(
            $request->validated()
        );

        $audit->record(
            'catalog.item.updated',
            $request,
            CatalogItem::class,
            $item->id
        );

        return new CatalogItemResource($item);
    }

    public function destroy(
        Request $request,
        string $catalogItem,
        AuditLogger $audit
    ): Response {
        abort_unless(
            $request->user()->hasPermission('catalog.delete'),
            403
        );

        $item = $this->findItem($catalogItem);

        $audit->record(
            'catalog.item.deleted',
            $request,
            CatalogItem::class,
            $item->id
        );

        $item->delete();

        return response()->noContent();
    }

    private function findItem(string $publicId): CatalogItem
    {
        return CatalogItem::query()
            ->where('public_id', $publicId)
            ->firstOrFail();
    }
}
