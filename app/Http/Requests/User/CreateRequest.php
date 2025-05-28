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
            'avatar' => 'nullable|string|max:255',
            'id_verified' => 'required|in:none,approved',
            'role' => 'nullable|in:employee,user',
            'bank_details' => 'nullable|string',
         ];
    }
}
