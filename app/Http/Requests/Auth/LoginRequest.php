<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'phone' => ['required', 'phone:AUTO'],
            'password' => 'required|string|min:6|max:255',
            'role' => 'required|in:user,admin',
        ];
    }
}
