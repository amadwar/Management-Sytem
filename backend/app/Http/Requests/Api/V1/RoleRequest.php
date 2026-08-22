<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class RoleRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name'=>['required','string','max:100'],
            'code'=>['required','alpha_dash','max:100'],
            'permission_ids'=>['array'],
            'permission_ids.*'=>['integer','exists:permissions,id'],
        ];
    }
}
