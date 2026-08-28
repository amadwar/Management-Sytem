<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Crm;

use Illuminate\Foundation\Http\FormRequest;

final class TagRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['name'=>['required','string','max:80'],'color'=>['nullable','regex:/^#[0-9A-Fa-f]{6}$/']]; }
}
