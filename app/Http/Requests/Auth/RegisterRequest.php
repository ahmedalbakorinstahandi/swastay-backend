<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends BaseFormRequest
{

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => ['required', 'phone:AUTO'],
            'password' => 'required|string|min:6|max:255',
            // 'country_code' => 'required|string|max:5',
        ];
    }
}
