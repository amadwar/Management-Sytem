<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Crm;

use Illuminate\Foundation\Http\FormRequest;

final class ContactRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'job_title' => ['nullable','string','max:150'],
            'email' => ['nullable','email','max:255'],
            'phone' => ['nullable','string','max:50'],
            'is_primary' => ['boolean'],
        ];
    }
}
