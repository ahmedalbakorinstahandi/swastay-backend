<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {

        $user = User::auth();
        
        $rules = [
            'listing_id' => 'required|exists:listings,id,deleted_at,NULL',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d|after:start_date',
            'message'    => 'required|string|max:500',
            'adults_count' => 'required|integer|min:0',
            'children_count' => 'required|integer|min:0',
            'infants_count' => 'required|integer|min:0',
            'pets_count' => 'required|integer|min:0',
        ];
        
        $admin_rules = [];
        if ($user->isAdmin()) {
            $admin_rules = [
                'guest_id' => 'required|exists:users,id,deleted_at,NULL',
                'admin_notes' => 'nullable|string|max:500',
            ];

            $rules = array_merge($rules, $admin_rules);
        }


        return $rules;
    }
}
