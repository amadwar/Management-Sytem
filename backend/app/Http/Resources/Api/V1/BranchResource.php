<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->public_id, 'name' => $this->name, 'code' => $this->code, 'address' => $this->address, 'phone' => $this->phone, 'latitude' => $this->latitude, 'longitude' => $this->longitude, 'is_active' => $this->is_active];
    }
}
