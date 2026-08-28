<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->public_id, 'name' => $this->name, 'email' => $this->email, 'phone' => $this->phone, 'status' => $this->status->value, 'roles' => $this->whenLoaded('roles', fn () => $this->roles->map(fn ($r) => ['id' => $r->public_id, 'name' => $r->name, 'code' => $r->code]))];
    }
}
