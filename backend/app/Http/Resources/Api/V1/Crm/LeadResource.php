<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id, 'name' => $this->name, 'company_name' => $this->company_name, 'email' => $this->email,
            'phone' => $this->phone, 'stage' => $this->stage->value, 'source' => $this->source, 'estimated_value' => $this->estimated_value,
            'currency_code' => $this->currency_code, 'assigned_to' => $this->assigned_to, 'notes' => $this->notes,
            'converted_at' => $this->converted_at?->toISOString(), 'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
