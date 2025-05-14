<?php

namespace App\Http\Requests\ListingReview;

use App\Http\Requests\BaseFormRequest;

class CreateRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id,deleted_at,NULL',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:2000',
        ];
    }
}
