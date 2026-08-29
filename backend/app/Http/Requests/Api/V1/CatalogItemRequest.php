<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CatalogItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in([
                    'product',
                    'service',
                ]),
            ],

            'sku' => [
                'nullable',
                'string',
                'max:100',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'currency_code' => [
                'required',
                'string',
                'size:3',
                'exists:currencies,code',
            ],

            'unit' => [
                'nullable',
                'string',
                'max:50',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'inactive',
                ]),
            ],

            'taxable' => [
                'required',
                'boolean',
            ],
        ];
    }
}
