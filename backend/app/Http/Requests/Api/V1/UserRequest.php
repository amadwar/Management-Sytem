<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

final class UserRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name'=>['required','string','max:255'],
            'email'=>['required','email','max:255'],
            'phone'=>['nullable','string','max:50'],
            'password'=>[$this->isMethod('post') ? 'required' : 'nullable','string','min:12','max:255'],
            'role_ids'=>['array'],
            'role_ids.*'=>['integer'],
        ];
    }
}
