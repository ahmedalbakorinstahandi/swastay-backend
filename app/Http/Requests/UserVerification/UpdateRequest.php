<?php

namespace App\Http\Requests\UserVerification;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends BaseFormRequest
{

    public function rules(): array
    {

        $rules = [
            'status' => 'required|in:in_review,approved,rejected',
        ];

        return $rules;
    }
}
