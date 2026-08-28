<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Crm;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->public_id,
            'type' => $this->type->value,
            'status' => $this->status->value,
            'name' => $this->name,
            'company_name' => $this->company_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'secondary_phone' => $this->secondary_phone,
            'tax_number' => $this->tax_number,
            'website' => $this->website,
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'source' => $this->source,
            'assigned_to' => $this->assigned_to,
            'description' => $this->description,
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->map(fn ($tag) => ['id' => $tag->id, 'public_id' => $tag->public_id, 'name' => $tag->name, 'color' => $tag->color])->values()),
            'contacts' => $this->whenLoaded('contacts', fn () => $this->contacts->map(fn ($contact) => ['id' => $contact->public_id, 'name' => $contact->name, 'job_title' => $contact->job_title, 'email' => $contact->email, 'phone' => $contact->phone, 'is_primary' => $contact->is_primary])->values()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
