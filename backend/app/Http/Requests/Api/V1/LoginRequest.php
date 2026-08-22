<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class LoginRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'workspace' => ['required','string','max:100'],
            'email' => ['required','email','max:255'],
            'password' => ['required','string','max:255'],
            'device_name' => ['nullable','string','max:100'],
        ];
    }
}
