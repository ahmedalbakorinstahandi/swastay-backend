<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'old_password' => 'nullable|string|min:6',
            'password' => 'nullable|string|min:6|confirmed',
            'avatar' => 'nullable|string|max:255',
            'bank_details' => 'nullable|string',
        ];
    }
}
