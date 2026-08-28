<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Crm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ActivityRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['call','email','meeting','task','other'])],
            'subject' => ['required','string','max:255'],
            'description' => ['nullable','string','max:5000'],
            'occurred_at' => ['required','date'],
        ];
    }
}
