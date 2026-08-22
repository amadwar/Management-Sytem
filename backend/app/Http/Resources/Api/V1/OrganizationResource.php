<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrganizationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id'=>$this->public_id,'legal_name'=>$this->legal_name,'display_name'=>$this->display_name,'email'=>$this->email,'phone'=>$this->phone,'tax_number'=>$this->tax_number,'address'=>$this->address];
    }
}
