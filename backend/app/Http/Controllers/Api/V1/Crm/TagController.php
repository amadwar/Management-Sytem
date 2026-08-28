<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Crm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Crm\TagRequest;
use App\Models\CrmTag;
use Illuminate\Support\Str;

final class TagController extends Controller
{
    public function index()
    {
        return response()->json(['data' => CrmTag::query()->orderBy('name')->get(['id', 'public_id', 'name', 'color'])]);
    }

    public function store(TagRequest $request)
    {
        abort_unless($request->user()->hasPermission('crm.tags.manage'), 403);
        $tag = CrmTag::query()->create(['public_id' => (string) Str::uuid(), ...$request->validated()]);

        return response()->json(['data' => $tag], 201);
    }
}
