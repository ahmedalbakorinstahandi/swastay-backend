<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:users,email,NULL,id,deleted_at,NULL',
            'phone' => ['required', 'phone:AUTO'],
            'password' => 'required|string|min:6',
            'wallet_balance' => 'nullable|numeric|min:0',
            'avatar' => 'nullable|string|max:255',
            'role' => 'required|in:user,admin',
            'status' => 'required|in:active,banneded',
            'host_verified' => 'required|in:none,in_review,approved,rejected,stopped',
            'is_verified' => 'boolean',
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
            'bank_details' => 'nullable|string',
        ];
    }
}
