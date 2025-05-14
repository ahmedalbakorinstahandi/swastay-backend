<?php

namespace App\Http\Requests\ListingReview;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;

class UpdateRequest extends BaseFormRequest
{
    public function rules(): array
    {

        $user = User::auth();


        if ($user && $user->isGuest()) {
            return [
                'rating'  => 'nullable|integer|min:1|max:5',
                'comment' => 'nullable|string|max:2000',
            ];
        }

        return [
            'block' => 'nullable|boolean',
        ];
    }
}
