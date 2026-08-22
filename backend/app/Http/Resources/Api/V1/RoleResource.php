<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id'=>$this->public_id,'name'=>$this->name,'code'=>$this->code,'is_system'=>$this->is_system,'permissions'=>$this->whenLoaded('permissions', fn()=> $this->permissions->map(fn($p)=>['id'=>$p->id,'code'=>$p->code,'name'=>$p->name]))];
    }
}
