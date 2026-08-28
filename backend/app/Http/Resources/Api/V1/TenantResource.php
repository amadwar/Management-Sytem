<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->public_id, 'slug' => $this->slug, 'status' => $this->status->value, 'default_locale' => $this->default_locale, 'timezone' => $this->timezone, 'organization' => OrganizationResource::make($this->whenLoaded('organization')), 'created_at' => $this->created_at?->toISOString()];
    }
}
