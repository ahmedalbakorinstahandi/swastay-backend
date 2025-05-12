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
            'email' => 'nullable|email|unique:users,email,' . $this->id . ',id,deleted_at,NULL',
            'phone' => ['nullable', 'phone:AUTO'],
            'password' => 'nullable|string|min:6',
            'wallet_balance' => 'nullable|numeric|min:0',
            'avatar' => 'nullable|string|max:255',
            'role' => 'nullable|in:user,admin',
            'status' => 'nullable|in:active,banneded',
            'host_verified' => 'nullable|in:none,in_review,approved,rejected,stopped',
            'is_verified' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
            'phone_verified' => 'nullable|boolean',
            'bank_details' => 'nullable|string',
        ];
    }
}
