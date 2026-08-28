<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Crm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CustomerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['person','company'])],
            'status' => ['required', Rule::in(['active','inactive','prospect','blocked'])],
            'name' => ['required','string','max:255'],
            'company_name' => ['nullable','string','max:255'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:50'],
            'secondary_phone' => ['nullable','string','max:50'],
            'tax_number' => ['nullable','string','max:100'],
            'website' => ['nullable','url','max:255'],
            'country_id' => ['nullable','integer','exists:countries,id'],
            'city_id' => ['nullable','integer','exists:cities,id'],
            'address' => ['nullable','string','max:255'],
            'postal_code' => ['nullable','string','max:30'],
            'source' => ['nullable','string','max:80'],
            'assigned_to' => ['nullable','integer', Rule::exists('users','id')->where(fn ($query) => $query->where('tenant_id', $this->user()?->tenant_id))],
            'custom_fields' => ['nullable','array'],
            'description' => ['nullable','string','max:5000'],
            'tag_ids' => ['sometimes','array'],
            'tag_ids.*' => ['integer', Rule::exists('crm_tags','id')->where(fn ($query) => $query->where('tenant_id', $this->user()?->tenant_id))],
        ];
    }
}
