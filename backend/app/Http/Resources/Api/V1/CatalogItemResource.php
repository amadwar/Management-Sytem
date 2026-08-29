<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CatalogItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'type' => $this->type,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency_code' => $this->currency_code,
            'unit' => $this->unit,
            'status' => $this->status,
            'taxable' => $this->taxable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
