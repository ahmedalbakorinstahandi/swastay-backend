<?php

namespace App\Http\Requests\UserVerification;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends BaseFormRequest
{

    public function rules(): array
    {


        $rules = [
            'file_path' => 'required|string',
            'type' => 'required|in:id_front,id_back,selfie,video,passport',
        ];


        $user = User::auth();


        if ($user->isAdmin()) {
            $rules['user_id'] = 'required|exists:users,id';
        }


        return $rules;
    }
}
