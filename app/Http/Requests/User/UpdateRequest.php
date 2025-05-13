<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'phone' => ['nullable', 'phone:AUTO'],
            'password' => 'nullable|string|min:6',
            'avatar' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,banneded',
            'id_verified' => 'nullable|in:none,approved,stopped,rejected',
            'bank_details' => 'nullable|string',
        ];
    }
}
